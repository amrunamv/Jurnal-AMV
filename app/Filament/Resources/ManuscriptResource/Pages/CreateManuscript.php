<?php

namespace App\Filament\Resources\ManuscriptResource\Pages;

use App\Filament\Resources\ManuscriptResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManuscript extends CreateRecord
{
    protected static string $resource = ManuscriptResource::class;

    public function getTitle(): string
    {
        return 'Tambah Manuskrip';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['submitter_id'] = auth()->id();
        $data['status'] = \App\Models\Manuscript::STATUS_DRAFT;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
