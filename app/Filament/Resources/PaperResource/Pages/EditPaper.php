<?php

namespace App\Filament\Resources\PaperResource\Pages;

use App\Filament\Resources\PaperResource;
use App\Models\EmailTemplate;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class EditPaper extends EditRecord
{
    protected static string $resource = PaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendEmail')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->visible(fn () => auth()->user()->is_admin)
                ->requiresConfirmation()
                ->modalHeading('Send Email to Author')
                ->modalDescription('Are you sure you want to send the selected email template to the author?')
                ->action(function () {
                    $paper = $this->record;
                    $emailTemplateId = $this->data['email_template_id'] ?? null;

                    if (!$emailTemplateId) {
                        Notification::make()
                            ->title('No Email Template Selected')
                            ->danger()
                            ->send();
                        return;
                    }

                    $template = EmailTemplate::find($emailTemplateId);
                    
                    if (!$template) {
                        Notification::make()
                            ->title('Email Template Not Found')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        // Replace placeholders in template
                        $subject = $this->replacePlaceholders($template->subject, $paper);
                        $htmlContent = $this->replacePlaceholders($template->html_template, $paper);
                        
                        // Send email
                        Mail::send([], [], function ($message) use ($paper, $subject, $htmlContent, $template) {
                            $message->to($paper->cer_author_name)
                                ->subject($subject)
                                ->html($htmlContent)
                                ->from(
                                    $template->custom_from_email ?: config('mail.from.address'),
                                    $template->custom_from_name ?: config('mail.from.name')
                                );
                        });

                        Notification::make()
                            ->title('Email Sent Successfully')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to Send Email')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Agar vol_issue_id selected hai to Volume aur Issue set karo
        if (!empty($data['vol_issue_id'])) {
            $volIssue = \App\Models\VolIssue::find($data['vol_issue_id']);
            if ($volIssue) {
                $data['Volume'] = $volIssue->vol;
                $data['Issue'] = $volIssue->issues;
            }
        }
        
        // Remove virtual field vol_issue_id
        unset($data['vol_issue_id']);
        
        // Agar created_by nahi hai to current user ka ID set karo
        if (empty($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }

        return $data;
    }

    private function replacePlaceholders(string $content, $paper): string
    {
        $replacements = [
            '{paper_title}' => $paper->Title ?? '',
            '{author_name}' => $paper->author_name ?? '',
            '{author_email}' => $paper->cer_author_name ?? '',
            '{volume}' => $paper->Volume ?? '',
            '{issue}' => $paper->Issue ?? '',
            '{doi}' => $paper->DOI ?? '',
            '{publication_date}' => $paper->publication_date ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
