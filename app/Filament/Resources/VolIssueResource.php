<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolIssueResource\Pages;
use App\Models\VolIssue;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VolIssueResource extends Resource
{
    protected static ?string $model = VolIssue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Vol / Issue';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('vol')
                ->label('Volume')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('issues')
                ->label('Issue No')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('Month')
                ->label('Month')
                ->options([
                    'January'   => 'January',
                    'February'  => 'February',
                    'March'     => 'March',
                    'April'     => 'April',
                    'May'       => 'May',
                    'June'      => 'June',
                    'July'      => 'July',
                    'August'    => 'August',
                    'September' => 'September',
                    'October'   => 'October',
                    'November'  => 'November',
                    'December'  => 'December',
                ])
                ->required(),

            Forms\Components\TextInput::make('Year')
                ->label('Year')
                ->maxLength(10)
                ->required(),

            Forms\Components\Select::make('issues_type')
                ->label('Issue Type')
                ->options([
                    'Current' => 'Current',
                    'Archive' => 'Archive',
                ])
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('vol')->label('Volume')->sortable(),
                Tables\Columns\TextColumn::make('issues')->label('Issue No')->sortable(),
                Tables\Columns\TextColumn::make('Month')->label('Month'),
                Tables\Columns\TextColumn::make('Year')->label('Year')->sortable(),
                Tables\Columns\TextColumn::make('issues_type')->label('Issue Type')->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Actions\EditAction::make(),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVolIssues::route('/'),
            'create' => Pages\CreateVolIssue::route('/create'),
            'edit'   => Pages\EditVolIssue::route('/{record}/edit'),
        ];
    }
}
