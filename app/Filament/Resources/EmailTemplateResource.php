<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Email Templates';
    
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Hidden::make('form_id')
                    ->default(1),
                    
                Forms\Components\TextInput::make('email_template_name')
                    ->label('Template Name')
                    ->required()
                    ->maxLength(100),
                    
                Forms\Components\Select::make('email_status')
                    ->label('Status')
                    ->options([
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled',
                    ])
                    ->default('enabled')
                    ->required(),
                    
                Forms\Components\TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('email_from')
                    ->label('Email From')
                    ->options([
                        'admin' => 'Admin',
                        'client' => 'Client',
                        'form_email_field' => 'Form Email Field',
                        'custom' => 'Custom',
                        'none' => 'None',
                    ])
                    ->default('custom')
                    ->reactive(),
                    
                // Forms\Components\TextInput::make('custom_from_name')
                //     ->label('Custom From Name')
                //     ->maxLength(100)
                //     ->visible(fn ($get) => $get('email_from') === 'custom'),
                    
                // Forms\Components\TextInput::make('custom_from_email')
                //     ->label('Custom From Email')
                //     ->email()
                //     ->maxLength(100)
                //     ->visible(fn ($get) => $get('email_from') === 'custom'),
                    
                Forms\Components\Select::make('email_reply_to')
                    ->label('Reply To')
                    ->options([
                        'custom' => 'Custom',
                        'none' => 'None',
                    ])
                    ->default('none')
                    ->reactive(),
                    
                Forms\Components\TextInput::make('custom_reply_to_name')
                    ->label('Custom Reply To Name')
                    ->maxLength(100)
                    ->visible(fn ($get) => $get('email_reply_to') === 'custom'),
                    
                Forms\Components\TextInput::make('custom_reply_to_email')
                    ->label('Custom Reply To Email')
                    ->email()
                    ->maxLength(100)
                    ->visible(fn ($get) => $get('email_reply_to') === 'custom'),
                    
                Forms\Components\RichEditor::make('html_template')
                    ->label('HTML Template')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                    ])
                    ->extraInputAttributes([
                        'style' => 'min-height: 250px;',
                    ])
                    ->helperText('Available placeholders: {paper_title}, {author_name}, {email}, {position}, {country}, {affiliation}, {volume}, {issue}, {volume_issue}, {doi}, {file_link}, {certificate_link}, {fees_amount}, {reviewer_comments}, {publication_date}, {paper_id}, {paper_status}, {created_at} | Old format: {$ANSWER_field1} (Title), {$ANSWER_field2} (Author), {$ANSWER_field9} (Volume Issue), {$ANSWER_field20} (DOI), {$ANSWER_field21} (File link), {$ANSWER_field22} (Reviewer Comments), {$ANSWER_field24} (Certificate Link), {$ANSWER_core__submission_id} (Paper ID), {$ANSWER_core__submission_date} (Submission Date)'),
                    
                Forms\Components\Textarea::make('text_template')
                    ->label('Text Template')
                    ->rows(10)
                    ->columnSpanFull()
                    ->helperText('Available placeholders: {paper_title}, {author_name}, {email}, {position}, {country}, {affiliation}, {volume}, {issue}, {volume_issue}, {doi}, {file_link}, {certificate_link}, {fees_amount}, {reviewer_comments}, {publication_date}, {paper_id}, {paper_status}, {created_at} | Old format: {$ANSWER_field1} (Title), {$ANSWER_field2} (Author), {$ANSWER_field9} (Volume Issue), {$ANSWER_field20} (DOI), {$ANSWER_field21} (File link), {$ANSWER_field22} (Reviewer Comments), {$ANSWER_field24} (Certificate Link), {$ANSWER_core__submission_id} (Paper ID), {$ANSWER_core__submission_date} (Submission Date)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email_id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_template_name')
                    ->label('Template Name')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('email_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'enabled',
                        'danger' => 'disabled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('email_status')
                    ->options([
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled',
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
                    ->modalHeading('Delete Email Template')
                    ->modalDescription('This action cannot be undone. Enter the password to proceed.')
                    ->modalSubmitActionLabel('Delete Template')
                    ->action(function (array $data, $record) {
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
                        $record->delete();
                        \Filament\Notifications\Notification::make()
                            ->title('Email template deleted successfully.')
                            ->success()->send();
                    }),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([])
            ->defaultSort('email_id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
