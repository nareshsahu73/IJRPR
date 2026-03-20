<?php

namespace App\Filament\Resources\VolIssueResource\Pages;

use App\Filament\Resources\VolIssueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVolIssue extends CreateRecord
{
    protected static string $resource = VolIssueResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
