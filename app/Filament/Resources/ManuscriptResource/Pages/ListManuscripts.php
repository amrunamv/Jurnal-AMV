<?php

namespace App\Filament\Resources\ManuscriptResource\Pages;

use App\Filament\Resources\ManuscriptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManuscripts extends ListRecords
{
    protected static string $resource = ManuscriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Manuskrip'),
        ];
    }

    public function getTitle(): string
    {
        return 'Daftar Manuskrip';
    }
}
