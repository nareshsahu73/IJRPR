<?php

namespace App\Http\Controllers;

use App\Jobs\SendPaperReceivedEmail;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PaperController extends Controller
{
    public function index()
    {
        $papers = auth()->user()->papers()->latest()->get();
        return view('papers.index', compact('papers'));
    }

    public function create()
    {
        return view('papers.create');
    }

    public function store(Request $request)
    {
        // Verify reCAPTCHA v3
        $recaptchaToken = $request->input('g-recaptcha-response');
        if ($recaptchaToken) {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => env('RECAPTCHA_V3_SECRET_KEY'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);
            $result = $response->json();
            if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
                return back()->withErrors(['error' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
            }
        }

        $validated = $request->validate([
            'Title' => 'required|string|max:300',
            'author_name' => 'required|string|max:500',
            'cer_author_name' => 'required|email|max:255',
            'contact_no' => 'required|string|max:20',
            'affiliation' => 'required|string|max:255',
            'position' => 'required|string|in:UG Student,PG Student,PhD Student,Academic Person,Industry Person,Research Scholar,Other',
            'highest_qualification' => 'required|string|max:255',
            'Keywords' => 'required|string|max:255',
            'Abstract' => 'nullable|string|max:5000',
            'file' => 'required|file|max:15360',
        ], [
            'file.max' => 'Please Upload Word File (.docx) extension only, Maximum Size Allowed 15 MB Only. If file size above 15 MB then send paper to editor@ijrpr.com',
            'position.in' => 'Invalid position selected.',
        ]);

        // Manual DOCX validation — also check for double extensions like malware.php.docx
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        // Reject if filename has multiple extensions (e.g. file.php.docx)
        $nameParts = explode('.', $originalName);
        if (count($nameParts) > 2) {
            return back()->withErrors(['file' => 'Invalid file name. Only simple .docx files are allowed.'])->withInput();
        }

        // Check MIME type matches docx
        $mimeType = $file->getMimeType();
        $allowedMimes = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream',
            'application/zip', // docx is a zip internally
        ];

        if ($extension !== 'docx') {
            return back()->withErrors(['file' => 'Only DOCX files are allowed.'])->withInput();
        }

        if (!in_array($mimeType, $allowedMimes)) {
            return back()->withErrors(['file' => 'Invalid file type. Only DOCX files are allowed.'])->withInput();
        }

        try {
            // Store with safe random name to prevent path traversal
            $safeName = \Illuminate\Support\Str::random(40) . '.docx';
            $filePath = $file->storeAs('papers', $safeName, 'public');

            // Sanitize all text inputs to prevent XSS
            $paper = auth()->user()->papers()->create([
                'Title' => htmlspecialchars($validated['Title'], ENT_QUOTES, 'UTF-8'),
                'author_name' => htmlspecialchars($validated['author_name'], ENT_QUOTES, 'UTF-8'),
                'cer_author_name' => filter_var($validated['cer_author_name'], FILTER_SANITIZE_EMAIL),
                'contact_no' => htmlspecialchars($validated['contact_no'], ENT_QUOTES, 'UTF-8'),
                'affiliation' => htmlspecialchars($validated['affiliation'], ENT_QUOTES, 'UTF-8'),
                'position' => htmlspecialchars($validated['position'], ENT_QUOTES, 'UTF-8'),
                'highest_qualification' => htmlspecialchars($validated['highest_qualification'], ENT_QUOTES, 'UTF-8'),
                'Keywords' => htmlspecialchars($validated['Keywords'], ENT_QUOTES, 'UTF-8'),
                'Abstract' => !empty($validated['Abstract']) ? htmlspecialchars($validated['Abstract'], ENT_QUOTES, 'UTF-8') : null,
                'file_name' => $filePath,
                'original_filename' => $originalName,
                'paper_status' => 'PaperUnderReview',
                'created_by' => auth()->id(),
                'ip_address' => $request->ip(),
                'author_comment' => $request->input('author_comment') ? htmlspecialchars($request->input('author_comment'), ENT_QUOTES, 'UTF-8') : null,
            ]);

            // Dispatch email job to queue
            SendPaperReceivedEmail::dispatch($paper)->onQueue('default');

            return redirect()->route('papers.index')->with('success', 'Paper submitted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error submitting paper: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== (int) $paper->created_by || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('papers.show', compact('paper'));
    }

    public function download($id)
    {
        $paper = Paper::findOrFail($id);
        $user = auth()->user();
    
        if (!$user->is_admin && $user->id !== (int) $paper->created_by) {
            abort(403, 'Unauthorized access');
        }
    
        $filePath = storage_path('app/public/' . $paper->file_name);
    
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
    
        return response()->download($filePath);
    }

    public function destroy(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== (int) $paper->created_by && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized to delete this paper');
        }
        
        Storage::disk('public')->delete($paper->file_name);
        $paper->delete();

        return redirect()->route('papers.index')->with('success', 'Paper deleted successfully!');
    }

    public function checkStatus($id)
    {
        $paper = Paper::findOrFail($id);

        if (auth()->id() !== (int)$paper->created_by) {
            abort(403, 'Unauthorized access');
        }

        return view('papers.status', compact('paper'));
    }

    public function adminDownload(Request $request, $id)
    {
        $paper = Paper::findOrFail($id);
        
        // Check if user is admin
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }
        
        $type = $request->query('type', 'public');
        $field = $request->query('field', 'file_name');
        
        // Get the file name from the specified field
        $fileName = $paper->$field;
        
        if (!$fileName) {
            abort(404, 'File not found');
        }
        
        // Determine disk and path
        if ($type === 'private') {
            $filePath = storage_path('app/private/' . $fileName);
        } else {
            $filePath = storage_path('app/public/' . $fileName);
        }
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found at: ' . $filePath);
        }
        
        // Get original filename or use basename
        $downloadName = $paper->original_filename ?? basename($fileName);
        
        return response()->download($filePath, $downloadName);
    }
}
