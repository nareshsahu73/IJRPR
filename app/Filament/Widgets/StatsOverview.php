<?php

namespace App\Filament\Widgets;

use App\Models\Paper;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            
            Stat::make('Total Papers', Paper::count())
                ->description('All submitted papers')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            
            Stat::make('Papers This Month', Paper::whereMonth('created_at', now()->month)->count())
                ->description('Submitted this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }
}
