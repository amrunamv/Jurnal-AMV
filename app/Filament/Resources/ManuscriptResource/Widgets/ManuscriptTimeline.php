<?php

namespace App\Filament\Resources\ManuscriptResource\Widgets;

use App\Models\Manuscript;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class ManuscriptTimeline extends Widget
{
    protected static string $view = 'filament.resources.manuscript-resource.widgets.manuscript-timeline';

    public ?Model $record = null;

    protected function getStatuses(): array
    {
        return [
            Manuscript::STATUS_DRAFT => ['label' => 'Protocol Draft', 'icon' => 'heroicon-o-pencil-square'],
            Manuscript::STATUS_SUBMITTED => ['label' => 'Submission', 'icon' => 'heroicon-o-paper-airplane'],
            Manuscript::STATUS_EDITOR_REVIEW => ['label' => 'Editorial Vetting', 'icon' => 'heroicon-o-eye'],
            Manuscript::STATUS_UNDER_REVIEW => ['label' => 'Peer Review', 'icon' => 'heroicon-o-beaker'],
            Manuscript::STATUS_REVISION_REQUIRED => ['label' => 'Revision Cycle', 'icon' => 'heroicon-o-arrow-path'],
            Manuscript::STATUS_COPYEDITING => ['label' => 'Copyediting', 'icon' => 'heroicon-o-language'],
            Manuscript::STATUS_PRODUCTION => ['label' => 'Production', 'icon' => 'heroicon-o-cpu-chip'],
            Manuscript::STATUS_PUBLISHED => ['label' => 'Repository', 'icon' => 'heroicon-o-check-badge'],
        ];
    }

    public function getStatusProgress(): int
    {
        $statuses = array_keys($this->getStatuses());
        $currentIndex = array_search($this->record->status, $statuses);
        
        if ($currentIndex === false) return 0;
        
        return (int) (($currentIndex / (count($statuses) - 1)) * 100);
    }
}
