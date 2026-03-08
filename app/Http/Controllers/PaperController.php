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
            'cer_author_name' => 'required|email',
            'contact_no' => 'required|string|max:20',
            'affiliation' => 'required|string|max:255',
            'position' => 'required|string',
            'Abstract' => 'nullable|string',
            'file' => 'required|file|mimes:docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
        ]);

        try {
            $filePath = $request->file('file')->store('papers', 'public');

            auth()->user()->papers()->create([
                'Title' => $validated['Title'],
                'author_name' => $validated['author_name'],
                'cer_author_name' => $validated['cer_author_name'],
                'contact_no' => $validated['contact_no'],
                'affiliation' => $validated['affiliation'],
                'position' => $validated['position'],
                'Abstract' => $validated['Abstract'] ?? null,
                'file_name' => $filePath,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('papers.index')->with('success', 'Paper submitted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error submitting paper: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->created_by && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('papers.show', compact('paper'));
    }

    public function download(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->created_by && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $filePath = storage_path('app/public/' . $paper->file_name);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, basename($paper->file_name));
    }

    public function destroy(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->created_by && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized to delete this paper');
        }
        
        Storage::disk('public')->delete($paper->file_name);
        $paper->delete();

        return redirect()->route('papers.index')->with('success', 'Paper deleted successfully!');
    }
}
