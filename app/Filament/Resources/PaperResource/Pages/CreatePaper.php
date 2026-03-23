<?php

namespace App\Filament\Resources\PaperResource\Pages;

use App\Filament\Resources\PaperResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreatePaper extends CreateRecord
{
    protected static string $resource = PaperResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['vol_issue_id'])) {
            $volIssue = \App\Models\VolIssue::find($data['vol_issue_id']);
            if ($volIssue) {
                $data['Volume'] = $volIssue->vol;
                $data['Issue']  = $volIssue->issues;
            }
        }

        unset($data['vol_issue_id']);

        $required = ['Title', 'author_name', 'cer_author_name', 'contact_no', 'position', 'highest_qualification', 'affiliation', 'Keywords'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                \Filament\Notifications\Notification::make()
                    ->title(ucfirst(str_replace('_', ' ', $field)) . ' is required.')
                    ->danger()->send();
                $this->halt();
            }
        }

        if (empty($data['file_name'])) {
            \Filament\Notifications\Notification::make()
                ->title('Attach Paper is required.')
                ->danger()->send();
            $this->halt();
        }

        $data['created_by'] = auth()->id();
        $data['publication_date'] = now()->format('Y-m-d H:i:s');

        // Set default status if not already set
        if (empty($data['paper_status'])) {
            $data['paper_status'] = 'PaperUnderReview';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $paper = $this->record;

        if (!$paper->cer_author_name) {
            return;
        }

        try {
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

            // No attachment — plain confirmation email only
            Mail::send([], [], function ($message) use ($paper, $paperId, $html) {
                $message->to($paper->cer_author_name)
                    ->subject('Paper Received - IJRPR-' . $paperId)
                    ->html($html)
                    ->from(config('mail.from.address'), config('mail.from.name'));
                // Intentionally no attachment
            });

            \Filament\Notifications\Notification::make()
                ->title('Paper created and confirmation email sent to author.')
                ->success()->send();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Paper created but email failed: ' . $e->getMessage())
                ->warning()->send();
        }
    }
}
