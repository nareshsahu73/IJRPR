<?php

namespace App\Filament\Resources\VolIssueResource\Pages;

use App\Filament\Resources\VolIssueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolIssue extends EditRecord
{
    protected static string $resource = VolIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
