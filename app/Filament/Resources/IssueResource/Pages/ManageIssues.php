<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIssues extends ManageRecords
{
    protected static string $resource = IssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Nomor Isu'),
        ];
    }

    public function getTitle(): string
    {
        return 'Manajemen Nomor Isu';
    }
}
