<?php

namespace App\Filament\Resources\VolIssueResource\Pages;

use App\Filament\Resources\VolIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolIssues extends ListRecords
{
    protected static string $resource = VolIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
