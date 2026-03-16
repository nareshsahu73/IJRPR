<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Use the value from toggle, default to false if not set
        $data['is_admin'] = (bool) ($data['is_admin'] ?? false);
        
        // Remove password_confirmation as it's not needed in database
        unset($data['password_confirmation']);
        
        return $data;
    }
}
