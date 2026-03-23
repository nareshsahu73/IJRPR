<?php

namespace App\Filament\Resources\PaperResource\Pages;

use App\Filament\Resources\PaperResource;
use App\Models\Paper;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPapers extends ListRecords
{
    protected static string $resource = PaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $search = trim($this->tableSearch ?? '');

        if ($search === '') {
            return $query;
        }

        if (is_numeric($search)) {
            return $query->where('id', '=', (int) $search);
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('Title', 'like', "%{$search}%")
              ->orWhere('author_name', 'like', "%{$search}%")
              ->orWhere('cer_author_name', 'like', "%{$search}%");
        });
    }
}
