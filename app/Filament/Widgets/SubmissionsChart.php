<?php

namespace App\Filament\Widgets;

use App\Models\Manuscript;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubmissionsChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pengiriman Manuskrip';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Manuscript::query();

        if ($user && $user->hasRole('author')) {
            $query->where('submitter_id', $user->id);
        }

        // Generate the last 6 months list
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->translatedFormat('F');
            $months[] = $monthName;
            
            $count = (clone $query)
                ->whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->count();
                
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengiriman',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
