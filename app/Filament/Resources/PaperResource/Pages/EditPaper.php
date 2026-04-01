<?php

namespace App\Filament\Resources\PaperResource\Pages;

use App\Filament\Resources\PaperResource;
use App\Models\EmailTemplate;
use App\Models\Paper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class EditPaper extends EditRecord
{
    protected static string $resource = PaperResource::class;

    protected function getHeaderActions(): array
    {
        $prevPaper = Paper::where('id', '<', $this->record->id)->orderBy('id', 'desc')->first();
        $nextPaper = Paper::where('id', '>', $this->record->id)->orderBy('id', 'asc')->first();

        return [
            Actions\Action::make('prevPaper')
                ->label('← Previous')
                ->color('gray')
                ->disabled(!$prevPaper)
                ->url($prevPaper ? static::getResource()::getUrl('edit', ['record' => $prevPaper->id]) : '#'),

            Actions\Action::make('nextPaper')
                ->label('Next →')
                ->color('gray')
                ->disabled(!$nextPaper)
                ->url($nextPaper ? static::getResource()::getUrl('edit', ['record' => $nextPaper->id]) : '#'),

            Actions\Action::make('backToList')
                ->label('Back to Papers')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),

            Actions\Action::make('downloadPaper')
                ->label('Download Paper')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->file_name)
                ->action(function () {
                    return response()->download(
                        storage_path('app/public/' . $this->record->file_name),
                        basename($this->record->file_name)
                    );
                }),
            
            Actions\Action::make('downloadFormattedDoc')
                ->label('Download Formatted Doc')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->visible(fn () => (auth()->user()->is_admin || auth()->user()->is_staff) && $this->record->formatted_doc)
                ->action(function () {
                    return response()->download(
                        storage_path('app/private/' . $this->record->formatted_doc),
                        basename($this->record->formatted_doc)
                    );
                }),
            
            Actions\Action::make('downloadPlagiarismReport')
                ->label('Download Plagiarism Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->visible(fn () => (auth()->user()->is_admin || auth()->user()->is_staff) && $this->record->plagiarism_report)
                ->action(function () {
                    return response()->download(
                        storage_path('app/private/' . $this->record->plagiarism_report),
                        basename($this->record->plagiarism_report)
                    );
                }),
            
            Actions\Action::make('deleteWithPassword')
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->hidden(fn () => !auth()->user()->is_admin)
                ->form([
                    \Filament\Forms\Components\TextInput::make('delete_password')
                        ->label('Delete Password')
                        ->password()
                        ->required(),
                ])
                ->modalHeading('Delete Paper')
                ->modalDescription('This action cannot be undone. Enter the password to proceed.')
                ->modalSubmitActionLabel('Delete Paper')
                ->action(function (array $data) {
                    $attempts = session()->get('delete_attempts', 0);

                    if ($attempts >= 10) {
                        \Filament\Notifications\Notification::make()
                            ->title('Too many attempts. Access locked for this session.')
                            ->danger()->send();
                        return;
                    }

                    if ($data['delete_password'] !== env('DELETE_PASSWORD')) {
                        session()->put('delete_attempts', $attempts + 1);
                        $remaining = 10 - ($attempts + 1);
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect password. ' . $remaining . ' attempts remaining.')
                            ->danger()->send();
                        return;
                    }

                    session()->forget('delete_attempts');
                    $this->record->delete();

                    \Filament\Notifications\Notification::make()
                        ->title('Paper deleted successfully.')
                        ->success()->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }

    protected function getFormActions(): array
    {
        $prevPaper = Paper::where('id', '<', $this->record->id)->orderBy('id', 'desc')->first();
        $nextPaper = Paper::where('id', '>', $this->record->id)->orderBy('id', 'asc')->first();

        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            Actions\Action::make('prevPaperBottom')
                ->label('← Previous')
                ->color('gray')
                ->disabled(!$prevPaper)
                ->url($prevPaper ? static::getResource()::getUrl('edit', ['record' => $prevPaper->id]) : '#'),

            Actions\Action::make('nextPaperBottom')
                ->label('Next →')
                ->color('gray')
                ->disabled(!$nextPaper)
                ->url($nextPaper ? static::getResource()::getUrl('edit', ['record' => $nextPaper->id]) : '#'),
            Actions\Action::make('sendEmail')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->visible(fn () => auth()->user()->is_admin || auth()->user()->is_staff)
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
                        $subject = $this->replacePlaceholders($template->subject, $paper);
                        $htmlContent = $this->replacePlaceholders($template->html_template, $paper);
                        
                        Mail::send([], [], function ($message) use ($paper, $subject, $htmlContent, $template) {
                            if ($template->email_reply_to === 'custom' && $template->custom_reply_to_email) {
                                $fromEmail = $template->custom_reply_to_email;
                                $fromName = $template->custom_reply_to_name ?: $template->custom_reply_to_email;
                            } else {
                                $fromEmail = config('mail.from.address');
                                $fromName = config('mail.from.name');
                            }

                            $message->to($paper->cer_author_name)
                                ->subject($subject)
                                ->html($htmlContent)
                                ->from($fromEmail, $fromName);

                            // Attach plagiarism report only when paper status is PaperRejected
                            if ($paper->paper_status === 'PaperRejected' && $paper->plagiarism_report) {
                                $filePath = storage_path('app/private/' . $paper->plagiarism_report);
                                if (file_exists($filePath)) {
                                    $message->attach($filePath, ['as' => basename($paper->plagiarism_report)]);
                                }
                            }
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
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['vol_issue_id'])) {
            $volIssue = \App\Models\VolIssue::find($data['vol_issue_id']);
            if ($volIssue) {
                $data['Volume'] = $volIssue->vol;
                $data['Issue'] = $volIssue->issues;
            }
        }
        
        // Remove virtual field vol_issue_id
        unset($data['vol_issue_id']);
        
        // IMPORTANT: Don't change created_by - keep original user
        unset($data['created_by']);

        // If plagiarism_checked_by is already set by a different user, don't overwrite it
        $record = $this->record;
        if ($record && $record->plagiarism_checked_by && $record->plagiarism_checked_by !== auth()->id()) {
            $data['plagiarism_checked_by'] = $record->plagiarism_checked_by;
        }
        
        return $data;
    }

    private function replacePlaceholders(string $content, $paper): string
    {
        // New format placeholders
        $replacements = [
            '{paper_title}'      => $paper->Title ?? '',
            '{author_name}'      => $paper->author_name ?? '',
            '{email}'            => $paper->cer_author_name ?? '',
            '{author_email}'     => $paper->cer_author_name ?? '',
            '{contact_no}'       => $paper->contact_no ?? '',
            '{affiliation}'      => $paper->affiliation ?? '',
            '{position}'         => $paper->position ?? '',
            '{country}'          => $paper->Keywords ?? '',
            '{volume}'           => $paper->Volume ?? '',
            '{issue}'            => $paper->Issue ?? '',
            '{doi}'              => $paper->DOI ?? '',
            '{publication_date}' => $paper->publication_date ?? '',
            '{paper_id}'         => $paper->id ?? '',
            '{submission_date}'  => $paper->created_at ?? '',
            '{created_at}'       => $paper->created_at ?? '',
            '{paper_status}'     => $paper->paper_status ?? '',
            '{file_link}'        => $paper->Reference ?? '',
            '{certificate_link}' => $paper->certificate_link ?? '',
            '{fees_amount}'      => $paper->filled_copy_right ?? '',
            '{reviewer_comments}'=> $paper->more_data ?? '',
            '{volume_issue}'     => ($paper->Volume && $paper->Issue) ? "Volume {$paper->Volume} Issue {$paper->Issue}" : '',
        ];

        // Old format placeholders (for backward compatibility)
        $oldFormatReplacements = [
            '{$ANSWER_field1}'              => $paper->Title ?? '',
            '{$ANSWER_field2}'              => $paper->author_name ?? '',
            '{$ANSWER_field9}'              => ($paper->Volume && $paper->Issue) ? "Volume {$paper->Volume} Issue {$paper->Issue}" : '',
            '{$ANSWER_field20}'             => $paper->DOI ?? '',
            '{$ANSWER_field21}'             => $paper->Reference ?? '',
            '{$ANSWER_field22}'             => $paper->more_data ?? '',
            '{$ANSWER_field24}'             => $paper->certificate_link ?? '',
            '{$ANSWER_core__submission_id}' => $paper->id ?? '',
            '{$ANSWER_core__submission_date}' => $paper->created_at ?? '',
        ];

        $allReplacements = array_merge($replacements, $oldFormatReplacements);

        return str_replace(array_keys($allReplacements), array_values($allReplacements), $content);
    }
}
