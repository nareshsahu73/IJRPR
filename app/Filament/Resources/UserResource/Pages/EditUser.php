<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainPassword = null;
    protected bool $shareCredentials = false;

    // Holds pending data when OTP is required
    public bool $showOtpModal = false;
    public string $otpInput = '';
    public ?array $pendingSaveData = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToList')
                ->label('Back to Users')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),

            // OTP verify modal action — visible only when OTP is pending in session
            Actions\Action::make('verifyOtp')
                ->label('🔐 Verify OTP & Save')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->visible(fn () => 
                    session()->has('staff_otp') && 
                    auth()->user()?->is_admin &&
                    (int)session('staff_otp_uid') === (int)$this->record->id
                )
                ->form([
                    \Filament\Forms\Components\Placeholder::make('otp_info')
                        ->label('')
                        ->content('OTP has been sent to all admin emails. Enter the OTP to confirm staff role change.'),
                    \Filament\Forms\Components\TextInput::make('otp_code')
                        ->label('Enter OTP')
                        ->required()
                        ->numeric()
                        ->length(6)
                        ->placeholder('6-digit OTP'),
                ])
                ->modalHeading('OTP Verification — Staff Role Change')
                ->modalSubmitActionLabel('Verify & Apply Change')
                ->action(function (array $data) {
                    $this->verifyOtpAndSave($data['otp_code']);
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('saveWithOtp')
                ->label('Save')
                ->color('primary')
                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->mountUsing(function () {
                    // Clear stale OTP session if it's for a different user
                    if (session()->has('staff_otp_uid') && (int)session('staff_otp_uid') !== (int)$this->record->id) {
                        session()->forget(['staff_otp', 'staff_otp_expiry', 'staff_otp_uid', 'staff_otp_value']);
                    }

                    // Send OTP when modal is about to open, only if staff role is changing
                    $formData = $this->form->getState();
                    $currentIsStaff = (bool) $this->record->is_staff;
                    $newIsStaff     = (bool) ($formData['is_staff'] ?? false);

                    if ($currentIsStaff !== $newIsStaff && auth()->user()?->is_admin) {
                        $this->sendOtpToAdmins($currentIsStaff, $newIsStaff);
                    }
                })
                ->form([
                    \Filament\Forms\Components\Placeholder::make('otp_info')
                        ->label('')
                        ->content(function () {
                            $formData = $this->form->getState();
                            $currentIsStaff = (bool) $this->record->is_staff;
                            $newIsStaff     = (bool) ($formData['is_staff'] ?? false);
                            if ($currentIsStaff !== $newIsStaff) {
                                return new \Illuminate\Support\HtmlString('
                                    <div style="background:#eff6ff;border-left:4px solid #3b82f6;padding:12px 16px;border-radius:6px;margin-bottom:8px;">
                                        <p style="margin:0;font-weight:600;color:#1e40af;">📧 OTP Sent Successfully</p>
                                        <p style="margin:4px 0 0;color:#374151;font-size:14px;">A 6-digit OTP has been sent to all admin email addresses. Please check your email and enter the OTP below to confirm the staff role change.</p>
                                    </div>
                                ');
                            }
                            return 'Click Save to confirm changes.';
                        }),
                    \Filament\Forms\Components\TextInput::make('otp_code')
                        ->label('Enter 6-Digit OTP')
                        ->numeric()
                        ->placeholder('e.g. 123456')
                        ->extraInputAttributes([
                            'style' => 'font-size:22px;letter-spacing:10px;text-align:center;font-weight:bold;',
                            'maxlength' => '6',
                        ])
                        ->visible(function () {
                            $formData = $this->form->getState();
                            $currentIsStaff = (bool) $this->record->is_staff;
                            $newIsStaff     = (bool) ($formData['is_staff'] ?? false);
                            return $currentIsStaff !== $newIsStaff;
                        }),
                ])
                ->modalHeading('Save User')
                ->modalSubmitActionLabel('Save')
                ->action(function (array $data) {
                    $formData = $this->form->getState();
                    $currentIsStaff = (bool) $this->record->is_staff;
                    $newIsStaff     = (bool) ($formData['is_staff'] ?? false);

                    if ($currentIsStaff !== $newIsStaff) {
                        $this->verifyOtpAndSave($data['otp_code'] ?? '');
                    } else {
                        $this->save();
                    }
                }),
        ];
    }

    protected function handleSave(): void
    {
        $formData = $this->form->getState();

        $currentIsStaff = (bool) $this->record->is_staff;
        $newIsStaff     = (bool) ($formData['is_staff'] ?? false);

        // If staff role is being changed, send OTP first
        if ($currentIsStaff !== $newIsStaff && auth()->user()?->is_admin) {
            $this->pendingSaveData = $formData;
            $this->sendOtpToAdmins($currentIsStaff, $newIsStaff);
            $this->showOtpModal = true;
            return;
        }

        // No staff change — save normally
        $this->save();
    }    protected function sendOtpToAdmins(bool $currentIsStaff, bool $newIsStaff): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'staff_otp'        => $otp,
            'staff_otp_expiry' => now()->addMinutes(10),
            'staff_otp_uid'    => $this->record->id,
            'staff_otp_value'  => $newIsStaff,
        ]);

        $admins     = User::where('is_admin', 1)->get();
        $action     = $newIsStaff ? 'GRANT Staff Role' : 'REMOVE Staff Role';
        $targetName = $this->record->name;
        $targetEmail = $this->record->email;

        // First admin = To, rest = CC
        $primaryAdmin = $admins->first();
        $ccAdmins     = $admins->skip(1);

        if (!$primaryAdmin) {
            Notification::make()->title('No admin users found.')->danger()->send();
            return;
        }

        try {
            Mail::send([], [], function ($message) use ($primaryAdmin, $ccAdmins, $otp, $action, $targetName, $targetEmail) {
                $html = "
                    <h2>Staff Role Change — OTP Verification</h2>
                    <p>Hello,</p>
                    <p>A request has been made to <strong>{$action}</strong> for user:</p>
                    <p><strong>{$targetName}</strong> ({$targetEmail})</p>
                    <p>Your OTP is:</p>
                    <h1 style='font-size:40px;color:#1e40af;letter-spacing:10px;font-weight:bold;'>{$otp}</h1>
                    <p>This OTP expires in <strong>10 minutes</strong>.</p>
                    <p>If you did not initiate this request, please contact your team immediately.</p>
                    <br><p>IJRPR Admin System</p>
                ";

                $message->to($primaryAdmin->email, $primaryAdmin->name)
                    ->subject('OTP: Staff Role Change — IJRPR')
                    ->html($html);

                // CC all other admins
                foreach ($ccAdmins as $admin) {
                    $message->cc($admin->email, $admin->name);
                }
            });

            Notification::make()
                ->title('OTP sent to ' . $admins->count() . ' admin email(s). Click "Verify OTP & Save" button.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to send OTP: ' . $e->getMessage())
                ->danger()
                ->send();
            $this->showOtpModal = false;
        }
    }

    protected function verifyOtpAndSave(string $enteredOtp): void
    {
        $storedOtp  = session('staff_otp');
        $expiry     = session('staff_otp_expiry');
        $targetUid  = session('staff_otp_uid');
        $newStaff   = session('staff_otp_value');

        if (!$storedOtp) {
            Notification::make()->title('No OTP found. Please try saving again.')->danger()->send();
            return;
        }

        if (now()->isAfter($expiry)) {
            session()->forget(['staff_otp', 'staff_otp_expiry', 'staff_otp_uid', 'staff_otp_value']);
            Notification::make()->title('OTP expired. Please try saving again.')->danger()->send();
            $this->showOtpModal = false;
            return;
        }

        if ((int)$targetUid !== (int)$this->record->id) {
            Notification::make()->title('OTP mismatch. Please try again.')->danger()->send();
            return;
        }

        if ($enteredOtp !== $storedOtp) {
            Notification::make()->title('Invalid OTP. Please try again.')->danger()->send();
            return;
        }

        // OTP valid — apply staff change
        $this->record->update(['is_staff' => $newStaff]);
        session()->forget(['staff_otp', 'staff_otp_expiry', 'staff_otp_uid', 'staff_otp_value']);
        $this->showOtpModal = false;

        // Now save rest of the form data (excluding is_staff)
        $this->save();

        Notification::make()
            ->title('Staff role ' . ($newStaff ? 'granted' : 'removed') . ' successfully.')
            ->success()
            ->send();
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

        // is_staff handled via OTP flow — don't change via normal save
        unset($data['is_staff']);

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
