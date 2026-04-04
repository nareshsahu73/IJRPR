<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainPassword = null;
    protected bool $shareCredentials = false;

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
            Actions\Action::make('saveNormal')
                ->label('Save')
                ->color('primary')
                ->action(function () {
                    $this->save();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->plainPassword    = filled($data['password'] ?? '') ? $data['password'] : null;
        $this->shareCredentials = (bool) ($data['share_credentials'] ?? false);

        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['password_confirmation'], $data['share_credentials']);

        // Never allow is_admin to be changed via GUI
        unset($data['is_admin']);

        return $data;
    }

    protected function afterSave(): void
    {
        CreateUser::sendUserEmail(
            $this->record,
            $this->plainPassword,
            $this->shareCredentials,
            isNew: false
        );
    }
}
