<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToList')
                ->label('← Back to Templates')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Actions\Action::make('deleteWithPassword')
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('delete_password')
                        ->label('Delete Password')
                        ->password()
                        ->required(),
                ])
                ->modalHeading('Delete Email Template')
                ->modalDescription('This action cannot be undone. Enter the password to proceed.')
                ->modalSubmitActionLabel('Delete Template')
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
                        ->title('Email template deleted successfully.')
                        ->success()->send();
                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
