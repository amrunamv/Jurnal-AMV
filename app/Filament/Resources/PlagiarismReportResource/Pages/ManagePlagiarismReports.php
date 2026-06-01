<?php

namespace App\Filament\Resources\PlagiarismReportResource\Pages;

use App\Filament\Resources\PlagiarismReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePlagiarismReports extends ManageRecords
{
    protected static string $resource = PlagiarismReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
