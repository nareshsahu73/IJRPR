<?php

namespace App\Jobs;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaperReceivedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Paper $paper) {}

    public function handle(): void
    {
        $paper = $this->paper;

        if (!$paper->cer_author_name) {
            return;
        }

        $authorName = $paper->author_name ?? 'Author';
        $paperId    = $paper->id;
        $paperTitle = $paper->Title ?? '';

        $html = "
            <p>Dear <strong>{$authorName},</strong></p>
            <p>Thank you for submitting your paper to <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
            <p>Your paper has been received successfully.</p>
            <p><strong>Paper ID:</strong> IJRPR-{$paperId}<br>
            <strong>Paper Title:</strong> {$paperTitle}</p>
            <p>We will review your paper and notify you about the status shortly.</p>
            <br>
            <p>With Warm Regards,<br>
            <strong>IJRPR Team</strong><br>
            <strong>International Journal of Research Publication and Reviews (IJRPR)</strong><br>
            <a href='http://www.ijrpr.com'>http://www.ijrpr.com</a></p>
        ";

        Mail::send([], [], function ($message) use ($paper, $html, $paperId) {
            $message->to($paper->cer_author_name)
                ->subject('Paper Received - IJRPR-' . $paperId)
                ->html($html)
                ->from(config('mail.from.address'), config('mail.from.name'));

            // Attach the paper file only if size <= 10MB to avoid memory issues
            // if ($paper->file_name) {
            //     $filePath = storage_path('app/public/' . $paper->file_name);
            //     if (file_exists($filePath) && filesize($filePath) <= 10 * 1024 * 1024) {
            //         $attachName = $paper->original_filename ?? basename($paper->file_name);
            //         $message->attach($filePath, ['as' => $attachName]);
            //     }
            // }
        });
    }
}
