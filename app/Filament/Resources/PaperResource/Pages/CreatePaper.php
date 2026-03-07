<?php

namespace App\Filament\Resources\PaperResource\Pages;

use App\Filament\Resources\PaperResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaper extends CreateRecord
{
    protected static string $resource = PaperResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Agar vol_issue_id selected hai to Volume aur Issue set karo
        if (!empty($data['vol_issue_id'])) {
            $volIssue = \App\Models\VolIssue::find($data['vol_issue_id']);
            if ($volIssue) {
                $data['Volume'] = $volIssue->vol;
                $data['Issue'] = $volIssue->issues;
            }
        }
        
        // Remove virtual field vol_issue_id
        unset($data['vol_issue_id']);
        
        // Set created_by to current user
        $data['created_by'] = auth()->id();

        return $data;
    }
}
