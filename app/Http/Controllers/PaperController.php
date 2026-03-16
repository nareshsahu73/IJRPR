<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use Illuminate\Http\Request;
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
            'file' => 'required|file|max:10240',
        ], [
            'file.max' => 'The file size must not exceed 10MB.',
            'position.in' => 'Invalid position selected.',
        ]);

        // Manual DOCX validation
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension !== 'docx') {
            return back()->withErrors(['file' => 'Only DOCX files are allowed.'])->withInput();
        }

        try {
            $filePath = $request->file('file')->store('papers', 'public');

            // Sanitize all text inputs to prevent XSS
            auth()->user()->papers()->create([
                'Title' => htmlspecialchars($validated['Title'], ENT_QUOTES, 'UTF-8'),
                'author_name' => htmlspecialchars($validated['author_name'], ENT_QUOTES, 'UTF-8'),
                'cer_author_name' => filter_var($validated['cer_author_name'], FILTER_SANITIZE_EMAIL),
                'contact_no' => htmlspecialchars($validated['contact_no'], ENT_QUOTES, 'UTF-8'),
                'affiliation' => htmlspecialchars($validated['affiliation'], ENT_QUOTES, 'UTF-8'),
                'position' => htmlspecialchars($validated['position'], ENT_QUOTES, 'UTF-8'),
                'highest_qualification' => htmlspecialchars($validated['highest_qualification'], ENT_QUOTES, 'UTF-8'),
                'Keywords' => htmlspecialchars($validated['Keywords'], ENT_QUOTES, 'UTF-8'),
                'Abstract' => $validated['Abstract'] ? htmlspecialchars($validated['Abstract'], ENT_QUOTES, 'UTF-8') : null,
                'file_name' => $filePath,
                'paper_status' => 'Under Review',
                'created_by' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);

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

    public function download(Paper $paper)
    {
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

    public function checkStatus(Paper $paper)
    {
        // Check if user owns the paper
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
