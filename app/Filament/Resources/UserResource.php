<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->is_admin || auth()->user()?->is_staff;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->is_admin || auth()->user()?->is_staff;
    }

    public static function canEdit($record): bool
    {
        // Staff can only edit non-staff, non-admin users
        if (auth()->user()?->is_staff && !auth()->user()?->is_admin) {
            return !$record->is_staff && !$record->is_admin;
        }
        return auth()->user()?->is_admin === true;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->is_admin === true;
    }

    // Mask email for staff users: s***@company.com
    public static function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $masked = substr($local, 0, 1) . str_repeat('*', max(3, strlen($local) - 1));
        return $masked . '@' . $domain;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name')
                    ->disabled(fn ($record) => $record !== null)
                    ->helperText(fn ($record) => $record !== null ? 'Name cannot be changed after creation.' : null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email')
                    ->disabled(fn ($record) => $record !== null)
                    ->formatStateUsing(fn ($state, $record) => ($record && $record->is_staff) ? self::maskEmail($state ?? '') : $state)
                    ->helperText(fn ($record) => $record !== null ? 'Email cannot be changed after creation.' : null),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->label('Password')
                    ->extraInputAttributes(['autocomplete' => 'new-password'])
                    ->disabled(fn ($record) => $record !== null && ($record->is_admin || $record->is_staff))
                    ->helperText(fn ($record) => match(true) {
                        $record === null => 'Set initial password.',
                        $record->is_admin => 'Admin password can only be changed via the Forgot Password link on login page.',
                        $record->is_staff => 'Staff password can only be reset via "Send Reset Link" button below.',
                        default => 'Leave blank to keep current password.',
                    }),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(false)
                    ->maxLength(255)
                    ->label('Confirm Password')
                    ->same('password')
                    ->extraInputAttributes(['autocomplete' => 'new-password'])
                    ->hidden(fn ($record) => $record !== null && ($record->is_admin || $record->is_staff))
                    ->helperText('Must match the password field'),

                // Send Reset Link button — only for staff users on edit page
                Forms\Components\Placeholder::make('staff_reset_link')
                    ->label('')
                    ->visible(fn ($record, $livewire) =>
                        $record !== null &&
                        $record->is_staff &&
                        auth()->user()?->is_admin &&
                        $livewire instanceof \Filament\Resources\Pages\EditRecord
                    )
                    ->content(fn ($record) => new \Illuminate\Support\HtmlString('
                        <button type="button"
                            onclick="sendStaffResetLink(' . ($record?->id ?? 0) . ')"
                            style="background:#1e40af;color:white;border:none;padding:8px 18px;border-radius:6px;font-size:14px;cursor:pointer;font-weight:600;">
                            📧 Send Password Reset Link to Staff
                        </button>
                        <span id="staff-reset-status-' . ($record?->id ?? 0) . '" style="margin-left:10px;font-size:13px;"></span>
                        <script>
                        function sendStaffResetLink(userId) {
                            var btn = event.target;
                            btn.disabled = true;
                            btn.innerText = "Sending...";
                            fetch("' . route('staff.password.reset.send') . '", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector(\'meta[name=csrf-token]\').content,
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({user_id: userId})
                            })
                            .then(r => r.json())
                            .then(d => {
                                btn.innerText = "📧 Send Password Reset Link to Staff";
                                btn.disabled = false;
                                document.getElementById("staff-reset-status-" + userId).innerText = d.message;
                            })
                            .catch(() => {
                                btn.innerText = "📧 Send Password Reset Link to Staff";
                                btn.disabled = false;
                                document.getElementById("staff-reset-status-" + userId).innerText = "Failed to send.";
                            });
                        }
                        </script>
                    ')),
                Forms\Components\Toggle::make('is_staff')
                    ->label('Staff')
                    ->helperText('Staff users have limited admin panel access')
                    ->onColor('info')
                    ->offColor('gray')
                    ->visible(fn () => auth()->user()?->is_admin === true),
                // is_admin toggle removed — admin users are set directly in database only
                Forms\Components\Toggle::make('share_credentials')
                    ->label('Share credentials to user by email')
                    ->helperText('If checked, email and password will be included in the welcome email')
                    ->onColor('warning')
                    ->offColor('gray')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()?->is_admin
                ? $query->where('is_admin', 0)
                : $query->where('is_admin', 0)->where('is_staff', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) => $record->is_staff ? self::maskEmail($state) : $state),
                Tables\Columns\IconColumn::make('is_staff')
                    ->boolean()
                    ->label('Staff'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_staff')
                    ->label('Staff')
                    ->options([
                        '1' => 'Staff',
                        '0' => 'Non-Staff',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->is_admin || (!$record->is_staff && !$record->is_admin)),
                Actions\Action::make('deleteWithPassword')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->visible(fn () => auth()->user()?->is_admin === true)
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('delete_password')
                            ->label('Delete Password')
                            ->password()
                            ->required(),
                    ])
                    ->modalHeading('Delete User')
                    ->modalDescription('This action cannot be undone. Enter the password to proceed.')
                    ->modalSubmitActionLabel('Delete User')
                    ->action(function ($record, array $data) {
                        $attempts = session()->get('delete_user_attempts', 0);

                        if ($attempts >= 10) {
                            \Filament\Notifications\Notification::make()
                                ->title('Too many attempts. Access locked for this session.')
                                ->danger()->send();
                            return;
                        }

                        if ($data['delete_password'] !== env('DELETE_PASSWORD')) {
                            session()->put('delete_user_attempts', $attempts + 1);
                            $remaining = 10 - ($attempts + 1);
                            \Filament\Notifications\Notification::make()
                                ->title('Incorrect password. ' . $remaining . ' attempts remaining.')
                                ->danger()->send();
                            return;
                        }

                        session()->forget('delete_user_attempts');
                        $record->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('User deleted successfully.')
                            ->success()->send();
                    }),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(function ($query) {
                return $query->orderByRaw('(is_admin = 1 OR is_staff = 1) DESC, created_at DESC');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
