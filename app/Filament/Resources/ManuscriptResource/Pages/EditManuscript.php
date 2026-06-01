<?php

namespace App\Filament\Resources\ManuscriptResource\Pages;

use App\Filament\Resources\ManuscriptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManuscript extends EditRecord
{
    protected static string $resource = ManuscriptResource::class;

    public function getTitle(): string
    {
        return 'Ubah Manuskrip';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ManuscriptResource\Widgets\ManuscriptTimeline::class,
        ];
    }
}
