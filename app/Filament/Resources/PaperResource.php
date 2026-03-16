<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaperResource\Pages;
use App\Models\Paper;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaperResource extends Resource
{
    protected static ?string $model = Paper::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Basic Fields (visible to all)

                Forms\Components\TextInput::make('id')
                    ->label('Paper ID *')
                    ->disabled()
                    ->maxLength(300),

                Forms\Components\Select::make('vol_issue_id')
                    ->label('Volume Issue')
                    ->options(function () {
                        return \App\Models\VolIssue::where('deleted', 0)
                            ->orderBy('vol', 'asc')
                            ->orderBy('issues', 'asc')
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => "Volume {$item->vol} Issue {$item->issues}"];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $volIssue = \App\Models\VolIssue::find($state);
                            if ($volIssue) {
                                $set('Volume', $volIssue->vol);
                                $set('Issue', $volIssue->issues);
                            }
                        }
                    })
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Hidden::make('Volume'),
                Forms\Components\Hidden::make('Issue'),

                Forms\Components\TextInput::make('Title')
                    ->label('Paper Title*')
                    ->required()
                    ->maxLength(300),

                Forms\Components\TextInput::make('author_name')
                    ->label('Corresponding Author Name *')
                    ->required()
                    ->maxLength(500),
                
                Forms\Components\TextInput::make('cer_author_name')
                    ->label('Corresponding Author Email *')
                    ->email()
                    ->required(),
                
                Forms\Components\TextInput::make('contact_no')
                    ->label('Phone No (with country code) *')
                    ->required()
                    ->maxLength(20),

                Forms\Components\Select::make('position')
                    ->label('Position/Post *')
                    ->options([
                        'UG Student' => 'UG Student',
                        'PG Student' => 'PG Student',
                        'PhD Student' => 'PhD Student',
                        'Academic Person' => 'Academic Person',
                        'Industry Person' => 'Industry Person',
                        'Research Scholar' => 'Research Scholar',
                        'Other' => 'Other',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('highest_qualification')
                ->label('Highest Qualification*')
                ->required()
                ->maxLength(500),

                Forms\Components\TextInput::make('affiliation')
                    ->label('Organization/Institute Name *')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('Keywords')
                    ->label('Country *')
                    ->maxLength(255),

                Forms\Components\Select::make('priority_status')
                    ->label('Priority Status')
                    ->options([
                        'High priority UG' => 'High priority UG',
                        'Medium Priority PG' => 'Medium Priority PG',
                        'Medium Priority Academic' => 'Medium Priority Academic',
                        'Low Priority Abroad' => 'Low Priority Abroad',
                    ])
                    ->placeholder('Select an option')
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\FileUpload::make('file_name')
                    ->label('Attach Paper *')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/msword'
                    ])
                    ->maxSize(20480)
                    ->directory('papers')
                    ->disk('public')
                    ->uploadingMessage('Uploading paper...')
                    ->helperText('DOCX and DOC files accepted (Max: 20MB)')
                    ->deletable(),

                Forms\Components\Textarea::make('Abstract')
                    ->label('Author Comment (If any, Optional)')
                    ->rows(5)
                    ->columnSpanFull(),

                // Admin Only Fields
                Forms\Components\Select::make('paper_status')
                    ->label('Paper Status')
                    ->options([
                        'Paper Accepted' => 'Paper Accepted',
                        'Paper Rejected' => 'Paper Rejected',
                        'Under Review' => 'Under Review',
                        'Paper Published' =>  'Paper Published',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('final_manuscript')
                    ->label('Final manuscript')
                    ->options([
                        'Received' => 'Received',
                        'Not Received' => 'Not Received',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('copy_right_received')
                    ->label('Copy Right Received')
                    ->options([
                        'Yes' => 'Yes',
                        'No' => 'No',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\TextInput::make('filled_copy_right')
                    ->label('Filled Copy Right')
                    ->maxLength(255)
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('status_of_payment')
                    ->label('Status of Payment')
                    ->options([
                        'Paid' => 'Paid',
                        'Unpaid' => 'Unpaid',
                        'Waived' => 'Waived',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\DatePicker::make('publication_date')
                    ->label('Date')
                    ->displayFormat('Y-m-d')
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\TextInput::make('updated_at')
                    ->label('Last modified')
                     ->disabled()
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Address')
                    ->disabled()
                    ->helperText('Automatically captured when paper is submitted')
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

               
                Forms\Components\TextInput::make('Reference')
                    ->label('File link')
                    ->maxLength(255)
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\TextInput::make('certificate_link')
                    ->label('Certificate Link')
                    ->maxLength(255)
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\FileUpload::make('formatted_doc')
                    ->label('Formatted Doc file')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/msword'
                    ])
                    ->maxSize(5120)
                    ->disk('private')
                    ->directory('secure_uploads/formatted_docs')
                    ->uploadingMessage('Uploading and scanning document...')
                    ->helperText('DOCX and DOC files accepted (Max: 5MB)')
                    ->deletable()
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\FileUpload::make('plagiarism_report')
                    ->label('Plagiarism Report')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/msword',
                        'text/html',
                        'application/xhtml+xml'
                    ])
                    ->maxSize(5120)
                    ->disk('private')
                    ->directory('secure_uploads/plagiarism_reports')
                    ->uploadingMessage('Uploading and scanning report...')
                    ->helperText('HTML, DOC, and DOCX files accepted (Max: 5MB)')
                    ->deletable()
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),



                Forms\Components\TextInput::make('invoice_no')
                    ->label('Invoice No')
                    ->maxLength(100)
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('plagiarism_checked_by')
                    ->label('Plagiarism Checked by User')
                    ->options(function () {
                        return \App\Models\User::where('is_admin', 1)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Select admin user')
                    ->disabled(function ($record) {
                        if ($record && $record->plagiarism_checked_by) {
                            return $record->plagiarism_checked_by !== auth()->id();
                        }
                        return false;
                    })
                    ->dehydrated(true)
                    ->helperText(function ($record) {
                        if ($record && $record->plagiarism_checked_by) {
                            $user = \App\Models\User::find($record->plagiarism_checked_by);
                            return $user ? 'Assigned to: ' . $user->name . ' (locked)' : null;
                        }
                        return 'Select the admin user who checked plagiarism';
                    })
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('cer_status')
                    ->label('Reviewer Checked')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                Forms\Components\Select::make('certificate_only')
                    ->label('Plagiarism Checked')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),

                 Forms\Components\Textarea::make('plagiarism_percentage')
                    ->label('Plagiarism percentage and comment')
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin),


                Forms\Components\Textarea::make('more_data')
                    ->label('Reviewer Comments')
                    ->rows(3)
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin)
                    ->columnSpanFull(),

                // Email Template Section (Admin Only - shown after save button)
                Forms\Components\Select::make('email_template_id')
                    ->label('Select Email Template')
                    ->options(function () {
                        return \App\Models\EmailTemplate::where('email_status', 1)
                            ->pluck('email_template_name', 'email_id')
                            ->toArray();
                    })
                    ->searchable()
                    ->visible(fn ($livewire) => 
                        auth()->check() && 
                        auth()->user()->is_admin && 
                        $livewire instanceof \Filament\Resources\Pages\EditRecord
                    )
                    ->columnSpanFull()
                    ->helperText('Select an email template and click "Send Email" button below'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Title')
                    ->label('Paper Title')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Corresponding Author Name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('cer_author_name')
                    ->label('Corresponding Author Email')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('paper_status')
                    ->label('Paper Status')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Paper Accepted' => 'success',
                        'Paper Rejected' => 'danger',
                        'Under Review' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('final_manuscript')
                    ->label('Final manuscript')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Received' => 'success',
                        'Not Received' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('cer_status')
                    ->label('Reviewer Checked')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((string)$state) {
                        '1' => 'Yes',
                        '0' => 'No',
                        default => 'No',
                    })
                    ->color(fn ($state): string => match ((string)$state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('certificate_only')
                    ->label('Plagiarism Checked')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => match ((string)$state) {
                        '1' => 'Yes',
                        '0' => 'No',
                        default => 'No',
                    })
                    ->color(fn ($state): string => match ((string)$state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('highest_qualification')
                    ->label('Highest Qualification')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contact_no')
                    ->label('Contact')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('position')
                    ->label('Position/Post')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'UG Student' => 'info',
                        'PG Student' => 'success',
                        'PhD Student' => 'warning',
                        'Academic Person' => 'primary',
                        'Industry Person' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('affiliation')
                    ->label('Organization')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Keywords')
                    ->label('Country')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Volume')
                    ->label('Vol')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Issue')
                    ->label('Issue')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('copy_right_received')
                    ->label('Copyright')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_of_payment')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                        'Waived' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('publication_date')
                    ->label('Published Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Reference')
                    ->label('File Link')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('certificate_link')
                    ->label('Certificate Link')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Modified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Volume')
                    ->label('Volume')
                    ->options(function () {
                        return Paper::query()
                            ->whereNotNull('Volume')
                            ->distinct()
                            ->pluck('Volume', 'Volume')
                            ->sort()
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('Issue')
                    ->label('Issue')
                    ->options(function () {
                        return Paper::query()
                            ->whereNotNull('Issue')
                            ->distinct()
                            ->pluck('Issue', 'Issue')
                            ->sort()
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('paper_status')
                    ->label('Paper Status')
                    ->options([
                        'Paper Accepted' => 'Paper Accepted',
                        'Paper Rejected' => 'Paper Rejected',
                        'Under Review' => 'Under Review',
                        'Paper Published' =>  'Paper Published',
                    ]),
                Tables\Filters\SelectFilter::make('position')
                    ->label('Position')
                    ->options([
                        'UG Student' => 'UG Student',
                        'PG Student' => 'PG Student',
                        'PhD Student' => 'PhD Student',
                        'Academic Person' => 'Academic Person',
                        'Industry Person' => 'Industry Person',
                        'Research Scholar' => 'Research Scholar',
                        'Other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status_of_payment')
                    ->label('Payment Status')
                    ->options([
                        'Paid' => 'Paid',
                        'Unpaid' => 'Unpaid',
                        'Waived' => 'Waived',
                    ]),
                Tables\Filters\SelectFilter::make('priority_status')
                    ->label('Priority Status')
                    ->options([
                        'High priority UG' => 'High priority UG',
                        'Medium Priority PG' => 'Medium Priority PG',
                        'Medium Priority Academic' => 'Medium Priority Academic',
                        'Low Priority Abroad' => 'Low Priority Abroad',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPapers::route('/'),
            'create' => Pages\CreatePaper::route('/create'),
            'edit' => Pages\EditPaper::route('/{record}/edit'),
        ];
    }
}
