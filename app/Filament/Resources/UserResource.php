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
        return auth()->user()?->is_admin === true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->label('Password')
                    ->extraInputAttributes(['autocomplete' => 'new-password'])
                    ->disabled(fn ($record) => $record !== null && $record->is_admin)
                    ->helperText(fn ($record) => ($record !== null && $record->is_admin)
                        ? 'Admin password can only be changed via the Forgot Password link on login page.'
                        : 'Leave blank to keep current password (when editing).'),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(fn ($record) => $record === null)
                    ->dehydrated(false)
                    ->maxLength(255)
                    ->label('Confirm Password')
                    ->same('password')
                    ->extraInputAttributes(['autocomplete' => 'new-password'])
                    ->helperText('Must match the password field'),
                Forms\Components\Toggle::make('is_staff')
                    ->label('Staff')
                    ->helperText('Staff users have limited admin panel access')
                    ->onColor('info')
                    ->offColor('gray'),
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
            ->modifyQueryUsing(fn ($query) => $query->where('is_admin', 0))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
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
                Actions\EditAction::make(),
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
