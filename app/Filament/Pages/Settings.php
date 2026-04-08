<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Settings';
    
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public ?array $data = [];
    
    public function getView(): string
    {
        return 'filament.pages.settings';
    }

    public function mount(): void
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }
        $this->form->fill([
            'mail_mailer'       => config('mail.mailers.smtp.transport', 'smtp'),
            'mail_host'         => config('mail.mailers.smtp.host', 'smtp.gmail.com'),
            'mail_port'         => config('mail.mailers.smtp.port', '587'),
            'mail_username'     => config('mail.mailers.smtp.username', ''),
            'mail_password'     => '', // never pre-fill password
            'mail_encryption'   => config('mail.mailers.smtp.encryption', 'tls'),
            'mail_from_address' => config('mail.from.address', 'noreply@example.com'),
            'mail_from_name'    => config('mail.from.name', 'IJRPR'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('mail_mailer')
                    ->label('Mail Driver')
                    ->options([
                        'smtp' => 'SMTP',
                        'sendmail' => 'Sendmail',
                        'mailgun' => 'Mailgun',
                        'ses' => 'Amazon SES',
                    ])
                    ->required()
                    ->default('smtp'),

                Forms\Components\TextInput::make('mail_host')
                    ->label('SMTP Host')
                    ->required()
                    ->placeholder('smtp.gmail.com'),

                Forms\Components\TextInput::make('mail_port')
                    ->label('SMTP Port')
                    ->required()
                    ->numeric()
                    ->default('587'),

                Forms\Components\TextInput::make('mail_username')
                    ->label('SMTP Username')
                    ->required()
                    ->email()
                    ->placeholder('your-email@gmail.com'),

                Forms\Components\TextInput::make('mail_password')
                    ->label('SMTP Password')
                    ->password()
                    ->revealable()
                    ->placeholder('SMTP Password')
                    ->extraInputAttributes([
                        'autocomplete' => 'new-password',
                        'data-lpignore' => 'true',
                        'data-form-type' => 'other',
                    ]),

                Forms\Components\Select::make('mail_encryption')
                    ->label('Encryption')
                    ->options([
                        'tls' => 'TLS',
                        'ssl' => 'SSL',
                    ])
                    ->required()
                    ->default('tls'),

                Forms\Components\TextInput::make('mail_from_address')
                    ->label('From Email Address')
                    ->email()
                    ->required()
                    ->placeholder('noreply@example.com'),

                Forms\Components\TextInput::make('mail_from_name')
                    ->label('From Name')
                    ->required()
                    ->default('IJRPR')
                    ->placeholder('IJRPR'),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $envContent = file_get_contents(base_path('.env'));

        $envVars = [
            'MAIL_MAILER'       => $data['mail_mailer'],
            'MAIL_HOST'         => $data['mail_host'],
            'MAIL_PORT'         => $data['mail_port'],
            'MAIL_USERNAME'     => $data['mail_username'],
            'MAIL_ENCRYPTION'   => $data['mail_encryption'],
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'],
            'MAIL_FROM_NAME'    => '"' . $data['mail_from_name'] . '"',
        ];

        // Only update password if a new one was entered
        if (!empty($data['mail_password'])) {
            $envVars['MAIL_PASSWORD'] = $data['mail_password'];
        }

        foreach ($envVars as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents(base_path('.env'), $envContent);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
