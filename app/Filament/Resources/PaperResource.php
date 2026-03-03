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
                Forms\Components\TextInput::make('title')
                    ->label('Title of Paper')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('corresponding_author_name')
                    ->label('Name of Author (Only Corresponding Author)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('All author(s) and co-author(s) must have their full names clearly written in the MS Word file')
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('corresponding_author_email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->helperText('The email address of the author submitting the paper (Corresponding Author)'),
                
                Forms\Components\TextInput::make('contact_no')
                    ->label('Contact No (With Country code)')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('affiliation')
                    ->label('Affiliation of Corresponding Author')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Name of College/University/Company/ of Corresponding Author')
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('position')
                    ->label('Position/Post of Author')
                    ->options([
                        'UG Student' => 'UG Student',
                        'PG Student' => 'PG Student',
                        'PhD Student' => 'PhD Student',
                        'Academic Person' => 'Academic Person',
                        'Industry Person' => 'Industry Person',
                        'Other' => 'Other',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('country_name')
                    ->label('Country Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('file_path')
                    ->label('Upload Paper (Submit only MS word file DOC,DOCX file only)')
                    ->acceptedFileTypes(['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(10240)
                    ->directory('papers')
                    ->required()
                    ->helperText('Upload 1 supported file: document. Max 10 MB.')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Additional Notes (Optional)')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->visible(fn () => auth()->check() && auth()->user()->is_admin)
                    ->helperText('Leave empty to assign to current user'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Paper Title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('corresponding_author_name')
                    ->label('Author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
