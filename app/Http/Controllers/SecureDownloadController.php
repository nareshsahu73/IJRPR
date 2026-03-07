<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Services\SecureFileUploadService;
use Illuminate\Http\Request;

class SecureDownloadController extends Controller
{
    protected $fileService;

    public function __construct(SecureFileUploadService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Secure paper download with authentication and authorization
     */
    public function downloadPaper(Request $request, $id)
    {
        // Find paper
        $paper = Paper::findOrFail($id);

        // Authorization check
        if (!$this->canDownload($paper)) {
            abort(403, 'You do not have permission to download this file.');
        }

        // Log download attempt
        \Log::info('Paper Download', [
            'paper_id' => $paper->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        // Serve file securely
        return $this->fileService->serveFile(
            $paper->file_name,
            $paper->Title . '.docx'
        );
    }

    /**
     * Check if user can download the paper
     */
    private function canDownload(Paper $paper): bool
    {
        // Admin can download all papers
        if (auth()->check() && auth()->user()->is_admin) {
            return true;
        }

        // Author can download their own paper
        if (auth()->check() && auth()->id() === $paper->created_by) {
            return true;
        }

        // Otherwise deny
        return false;
    }
}
