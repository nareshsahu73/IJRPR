<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Email Templates';
    
    protected static ?int $navigationSort = 4;

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
                Tables\Columns\TextColumn::make('email_trigger_set')
                    ->label('Trigger')
                    ->badge()
                    ->default('custom'),
                Tables\Columns\TextColumn::make('custom_from_email')
                    ->label('From')
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('email_status')
                    ->options([
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled',
                    ]),
            ])
            ->defaultSort('email_id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
        ];
    }
}
