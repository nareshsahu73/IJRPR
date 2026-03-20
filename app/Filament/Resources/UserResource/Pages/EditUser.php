<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToList')
                ->label('Back to Users')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            // Normal save — blocks any is_admin change without password
            Actions\Action::make('saveNormal')
                ->label('Save')
                ->color('primary')
                ->action(function () {
                    $formData   = $this->form->getState();
                    $newIsAdmin = (bool) ($formData['is_admin'] ?? false);
                    $wasAdmin   = (bool) ($this->record->is_admin ?? false);

                    if ($newIsAdmin !== $wasAdmin) {
                        $action = $newIsAdmin ? '"Grant Admin Access"' : '"Revoke Admin Access"';
                        \Filament\Notifications\Notification::make()
                            ->title('Use ' . $action . ' button to change admin role.')
                            ->warning()->send();
                        return;
                    }

                    $this->save();
                }),

            // Grant admin — only visible when user is NOT admin
            Actions\Action::make('grantAdmin')
                ->label('Grant Admin Access')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->visible(fn () => ! (bool) $this->record->is_admin && ! (bool) $this->record->is_staff)
                ->form([
                    \Filament\Forms\Components\TextInput::make('admin_password')
                        ->label('Enter Security Password')
                        ->password()
                        ->required(),
                ])
                ->modalHeading('Grant Admin Access')
                ->modalDescription('Enter the security password to grant admin access.')
                ->modalSubmitActionLabel('Confirm & Grant')
                ->action(function (array $data) {
                    $attempts = session()->get('admin_grant_attempts', 0);
                    if ($attempts >= 10) {
                        \Filament\Notifications\Notification::make()
                            ->title('Too many attempts. Access locked.')->danger()->send();
                        return;
                    }
                    if ($data['admin_password'] !== env('DELETE_PASSWORD')) {
                        session()->put('admin_grant_attempts', $attempts + 1);
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect password. ' . (10 - $attempts - 1) . ' attempts remaining.')
                            ->danger()->send();
                        return;
                    }
                    session()->forget('admin_grant_attempts');
                    $this->record->update(['is_admin' => true]);
                    \Filament\Notifications\Notification::make()
                        ->title('Admin access granted.')->success()->send();
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record->id]));
                }),

            // Revoke admin — only visible when user IS admin
            Actions\Action::make('revokeAdmin')
                ->label('Revoke Admin Access')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger')
                ->visible(fn () => (bool) $this->record->is_admin)
                ->form([
                    \Filament\Forms\Components\TextInput::make('admin_password')
                        ->label('Enter Security Password')
                        ->password()
                        ->required(),
                ])
                ->modalHeading('Revoke Admin Access')
                ->modalDescription('Enter the security password to remove admin access from this user.')
                ->modalSubmitActionLabel('Confirm & Revoke')
                ->action(function (array $data) {
                    $attempts = session()->get('admin_revoke_attempts', 0);
                    if ($attempts >= 10) {
                        \Filament\Notifications\Notification::make()
                            ->title('Too many attempts. Access locked.')->danger()->send();
                        return;
                    }
                    if ($data['admin_password'] !== env('DELETE_PASSWORD')) {
                        session()->put('admin_revoke_attempts', $attempts + 1);
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect password. ' . (10 - $attempts - 1) . ' attempts remaining.')
                            ->danger()->send();
                        return;
                    }
                    session()->forget('admin_revoke_attempts');
                    $this->record->update(['is_admin' => false]);
                    \Filament\Notifications\Notification::make()
                        ->title('Admin access revoked.')->success()->send();
                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record->id]));
                }),

        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password only if it's provided
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password field if empty (keep existing password)
            unset($data['password']);
        }
        
        // Remove password_confirmation as it's not needed in database
        unset($data['password_confirmation']);

        // Staff users cannot be admin
        if (!empty($data['is_staff'])) {
            $data['is_admin'] = false;
        }
        
        return $data;
    }
}
