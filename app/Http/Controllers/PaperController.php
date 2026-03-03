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
            'title' => 'required|string|max:255',
            'corresponding_author_name' => 'required|string|max:255',
            'corresponding_author_email' => 'required|email|max:255',
            'contact_no' => 'required|string|max:20',
            'affiliation' => 'required|string|max:255',
            'position' => 'required|string',
            'country_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:doc,docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
        ]);

        try {
            $filePath = $request->file('file')->store('papers', 'public');

            auth()->user()->papers()->create([
                'title' => $validated['title'],
                'corresponding_author_name' => $validated['corresponding_author_name'],
                'corresponding_author_email' => $validated['corresponding_author_email'],
                'contact_no' => $validated['contact_no'],
                'affiliation' => $validated['affiliation'],
                'position' => $validated['position'],
                'country_name' => $validated['country_name'],
                'description' => $validated['description'] ?? null,
                'file_path' => $filePath,
            ]);

            return redirect()->route('papers.index')->with('success', 'Paper submitted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error submitting paper: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('papers.show', compact('paper'));
    }

    public function download(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $filePath = storage_path('app/public/' . $paper->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, basename($paper->file_path));
    }

    public function destroy(Paper $paper)
    {
        // Check if user owns the paper or is admin
        if (auth()->id() !== $paper->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized to delete this paper');
        }
        
        Storage::disk('public')->delete($paper->file_path);
        $paper->delete();

        return redirect()->route('papers.index')->with('success', 'Paper deleted successfully!');
    }
}
