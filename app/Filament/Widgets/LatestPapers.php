<?php

namespace App\Filament\Widgets;

use App\Models\Paper;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPapers extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Papers')
            ->query(
                Paper::query()->latest('created_at')->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('Title')
                    ->label('Paper Title')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Author')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('position')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'UG Student' => 'info',
                        'PG Student' => 'success',
                        'PhD Student' => 'warning',
                        'Academic Person' => 'primary',
                        'Industry Person' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
