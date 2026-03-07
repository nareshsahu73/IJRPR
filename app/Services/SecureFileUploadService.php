<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureFileUploadService
{
    // Allowed MIME types for papers
    private const ALLOWED_MIME_TYPES = [
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    ];

    // Allowed extensions
    private const ALLOWED_EXTENSIONS = ['doc', 'docx'];

    // Magic bytes for file validation
    private const MAGIC_BYTES = [
        'doc' => ['D0CF11E0'], // MS Office old format
        'docx' => ['504B0304'], // ZIP format (DOCX is ZIP)
    ];

    // Max file size (10MB)
    private const MAX_FILE_SIZE = 10485760;

    /**
     * Validate and store uploaded file securely
     */
    public function validateAndStore(UploadedFile $file, string $directory = 'papers'): array
    {
        // Step 1: Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds 10MB limit.');
        }

        // Step 2: Validate extension (whitelist)
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('Invalid file type. Only DOC and DOCX files are allowed.');
        }

        // Step 3: Validate MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Invalid file format detected. File may be corrupted or disguised.');
        }

        // Step 4: Validate magic bytes (file signature)
        if (!$this->validateMagicBytes($file, $extension)) {
            throw new \Exception('File signature validation failed. This may not be a genuine document.');
        }

        // Step 5: Generate secure random filename
        $secureFilename = $this->generateSecureFilename($extension);

        // Step 6: Store in secure location with year/month structure
        $path = $this->getSecurePath($directory);
        $storedPath = $file->storeAs($path, $secureFilename, 'private');

        // Step 7: Log upload for audit
        $this->logUpload($file, $secureFilename);

        return [
            'path' => $storedPath,
            'filename' => $secureFilename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Validate file magic bytes (file signature)
     */
    private function validateMagicBytes(UploadedFile $file, string $extension): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 4);
        fclose($handle);

        $hex = strtoupper(bin2hex($bytes));

        // Check if file starts with valid magic bytes
        if (isset(self::MAGIC_BYTES[$extension])) {
            foreach (self::MAGIC_BYTES[$extension] as $validHex) {
                if (str_starts_with($hex, $validHex)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate secure random filename
     */
    private function generateSecureFilename(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;
    }

    /**
     * Get secure storage path with year/month structure
     */
    private function getSecurePath(string $directory): string
    {
        $year = date('Y');
        $month = date('m');
        return "{$directory}/{$year}/{$month}";
    }

    /**
     * Log file upload for security audit
     */
    private function logUpload(UploadedFile $file, string $secureFilename): void
    {
        \Log::info('File Upload', [
            'original_name' => $file->getClientOriginalName(),
            'secure_name' => $secureFilename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'ip_address' => request()->ip(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Serve file securely (for download)
     */
    public function serveFile(string $path, string $originalName)
    {
        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($path, $originalName);
    }
}
