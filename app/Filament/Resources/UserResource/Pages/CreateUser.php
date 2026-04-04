<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainPassword = null;
    protected bool $shareCredentials = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Capture before hashing/unsetting
        $this->plainPassword    = $data['password'] ?? null;
        $this->shareCredentials = (bool) ($data['share_credentials'] ?? false);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_staff'] = (bool) ($data['is_staff'] ?? false);
        $data['is_admin'] = false; // Admin can only be set directly in database

        unset($data['password_confirmation'], $data['share_credentials']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->sendUserEmail(
            $this->record,
            $this->plainPassword,
            $this->shareCredentials,
            isNew: true
        );
    }

    public static function sendUserEmail($user, ?string $plainPassword, bool $shareCredentials, bool $isNew = true): void
    {
        if (!$user->email) return;

        $name     = $user->name ?? 'User';
        $email    = $user->email;
        $loginUrl = url('/login');

        try {
            Mail::send([], [], function ($message) use ($name, $email, $loginUrl, $plainPassword, $shareCredentials, $isNew) {
                $credentialsBlock = '';
                if ($shareCredentials && $plainPassword) {
                    $credentialsBlock = "
                        <p><strong>Your login credentials:</strong></p>
                        <p>Email: <strong>{$email}</strong><br>
                        Password: <strong>{$plainPassword}</strong></p>
                        <p style='color:#c0392b;font-size:12px;'>Please change your password after first login.</p>
                    ";
                }

                $action = $isNew ? 'created' : 'updated';
                $subject = $isNew ? 'Your IJRPR Account Has Been Created' : 'Your IJRPR Account Has Been Updated';

                $html = "
                    <p>Dear <strong>{$name}</strong>,</p>
                    <p>Your account on <strong>IJRPR Admin Panel</strong> has been <strong>{$action}</strong>.</p>
                    {$credentialsBlock}
                    <p>You can login at: <a href='{$loginUrl}'>{$loginUrl}</a></p>
                    <br>
                    <p>With Regards,<br>
                    <strong>IJRPR Team</strong><br>
                    <strong>International Journal of Research Publication and Reviews (IJRPR)</strong><br>
                    <a href='http://www.ijrpr.com'>http://www.ijrpr.com</a></p>
                ";

                $message->to($email)
                    ->subject($subject)
                    ->html($html)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Filament\Notifications\Notification::make()
                ->title($isNew ? 'User created and welcome email sent.' : 'User updated and notification email sent.')
                ->success()->send();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Email failed: ' . $e->getMessage())
                ->warning()->send();
        }
    }
}
