<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SecureFileUploadService
{
    // Allowed MIME types for papers (DOCX only for maximum security)
    private const ALLOWED_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    ];

    // Allowed extensions (DOCX only)
    private const ALLOWED_EXTENSIONS = ['docx'];

    // Magic bytes for file validation
    private const MAGIC_BYTES = [
        'docx' => ['504B0304', '504B0506', '504B0708'], // ZIP format signatures (DOCX is ZIP)
    ];

    // Max file size (10MB)
    private const MAX_FILE_SIZE = 10485760;

    // Quarantine and permanent storage paths
    private const QUARANTINE_PATH = 'quarantine';
    private const PERMANENT_PATH = 'secure_uploads';

    /**
     * Validate and store uploaded file securely with quarantine process
     */
    public function validateAndStore(UploadedFile $file, string $directory = 'papers'): array
    {
        try {
            // Step 1: Basic validation
            $this->validateBasicRequirements($file);

            // Step 2: Validate extension (whitelist)
            $extension = $this->validateExtension($file);

            // Step 3: Validate MIME type
            $this->validateMimeType($file);

            // Step 4: Validate magic bytes (file signature)
            $this->validateMagicBytes($file, $extension);

            // Step 5: Generate secure random filename
            $secureFilename = $this->generateSecureFilename($extension);

            // Step 6: Store in quarantine first
            $quarantinePath = $this->storeInQuarantine($file, $secureFilename);

            // Step 7: Scan for malware (basic content validation)
            $this->scanForMalware($quarantinePath);

            // Step 8: Move to permanent storage if clean
            $permanentPath = $this->moveToPermantentStorage($quarantinePath, $directory, $secureFilename);

            // Step 9: Log upload for audit
            $this->logUpload($file, $secureFilename, 'SUCCESS');

            return [
                'path' => $permanentPath,
                'filename' => $secureFilename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'clean',
            ];

        } catch (\Exception $e) {
            // Log security violation
            $this->logUpload($file, '', 'FAILED: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate basic file requirements
     */
    private function validateBasicRequirements(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds 10MB limit.');
        }

        // Check if file is valid
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        // Check for null bytes in filename (security)
        if (str_contains($file->getClientOriginalName(), "\0")) {
            throw new \Exception('Invalid filename detected.');
        }
    }

    /**
     * Validate file extension
     */
    private function validateExtension(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('Invalid file type. Only DOCX files are allowed.');
        }

        // Double extension check (security)
        $filename = $file->getClientOriginalName();
        if (substr_count($filename, '.') > 1) {
            throw new \Exception('Multiple file extensions detected. This is not allowed for security reasons.');
        }

        return $extension;
    }

    /**
     * Validate MIME type
     */
    private function validateMimeType(UploadedFile $file): void
    {
        // Get MIME type using finfo (more reliable)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        if (!in_array($detectedMime, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception("Invalid MIME type detected: {$detectedMime}. Only DOCX files are allowed.");
        }

        // Cross-check with Laravel's detection
        $laravelMime = $file->getMimeType();
        if (!in_array($laravelMime, self::ALLOWED_MIME_TYPES)) {
            throw new \Exception("MIME type validation failed: {$laravelMime}");
        }
    }

    /**
     * Validate file magic bytes (file signature)
     */
    private function validateMagicBytes(UploadedFile $file, string $extension): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            throw new \Exception('Cannot read file for signature validation.');
        }

        $bytes = fread($handle, 8); // Read more bytes for better detection
        fclose($handle);

        $hex = strtoupper(bin2hex($bytes));

        // Check if file starts with valid magic bytes
        $isValid = false;
        if (isset(self::MAGIC_BYTES[$extension])) {
            foreach (self::MAGIC_BYTES[$extension] as $validHex) {
                if (str_starts_with($hex, $validHex)) {
                    $isValid = true;
                    break;
                }
            }
        }

        if (!$isValid) {
            throw new \Exception('File signature validation failed. This may not be a genuine DOCX document.');
        }
    }

    /**
     * Generate secure random filename
     */
    private function generateSecureFilename(string $extension): string
    {
        return bin2hex(random_bytes(20)) . '_' . time() . '.' . $extension;
    }

    /**
     * Store file in quarantine folder first
     */
    private function storeInQuarantine(UploadedFile $file, string $filename): string
    {
        $quarantinePath = self::QUARANTINE_PATH . '/' . date('Y/m/d');
        
        // Ensure quarantine directory exists
        if (!Storage::disk('private')->exists($quarantinePath)) {
            Storage::disk('private')->makeDirectory($quarantinePath);
        }

        $fullPath = $quarantinePath . '/' . $filename;
        Storage::disk('private')->putFileAs($quarantinePath, $file, $filename);

        return $fullPath;
    }

    /**
     * Basic malware scanning (content validation)
     */
    private function scanForMalware(string $quarantinePath): void
    {
        $fullPath = Storage::disk('private')->path($quarantinePath);
        
        // Basic content scanning
        $content = file_get_contents($fullPath);
        
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '<?php',
            '<script',
            'javascript:',
            'vbscript:',
            'onload=',
            'onerror=',
            'eval(',
            'base64_decode',
            'shell_exec',
            'system(',
            'exec(',
            'passthru(',
            'file_get_contents',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                // Delete infected file
                Storage::disk('private')->delete($quarantinePath);
                throw new \Exception('Malicious content detected in file. Upload blocked for security.');
            }
        }

        // Additional DOCX specific validation
        if (!$this->validateDocxStructure($fullPath)) {
            Storage::disk('private')->delete($quarantinePath);
            throw new \Exception('Invalid DOCX file structure detected.');
        }
    }

    /**
     * Validate DOCX file structure
     */
    private function validateDocxStructure(string $filePath): bool
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== TRUE) {
                return false;
            }

            // Check for required DOCX files
            $requiredFiles = [
                '[Content_Types].xml',
                '_rels/.rels',
                'word/document.xml'
            ];

            foreach ($requiredFiles as $requiredFile) {
                if ($zip->locateName($requiredFile) === false) {
                    $zip->close();
                    return false;
                }
            }

            $zip->close();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move file from quarantine to permanent storage
     */
    private function moveToPermantentStorage(string $quarantinePath, string $directory, string $filename): string
    {
        $permanentPath = self::PERMANENT_PATH . '/' . $directory . '/' . date('Y/m');
        
        // Ensure permanent directory exists
        if (!Storage::disk('private')->exists($permanentPath)) {
            Storage::disk('private')->makeDirectory($permanentPath);
        }

        $finalPath = $permanentPath . '/' . $filename;
        
        // Move file from quarantine to permanent storage
        Storage::disk('private')->move($quarantinePath, $finalPath);

        return $finalPath;
    }

    /**
     * Log file upload for security audit
     */
    private function logUpload(UploadedFile $file, string $secureFilename, string $status): void
    {
        \Log::channel('security')->info('File Upload Attempt', [
            'status' => $status,
            'original_name' => $file->getClientOriginalName(),
            'secure_name' => $secureFilename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
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

        // Log download attempt
        \Log::channel('security')->info('File Download', [
            'path' => $path,
            'original_name' => $originalName,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
        ]);

        return Storage::disk('private')->download($path, $originalName);
    }

    /**
     * Clean old quarantine files (run via scheduled job)
     */
    public function cleanQuarantine(): void
    {
        $quarantineFiles = Storage::disk('private')->allFiles(self::QUARANTINE_PATH);
        
        foreach ($quarantineFiles as $file) {
            $lastModified = Storage::disk('private')->lastModified($file);
            
            // Delete files older than 24 hours
            if (time() - $lastModified > 86400) {
                Storage::disk('private')->delete($file);
            }
        }
    }
}
