<?php

namespace App\Filament\Pages;

use App\Models\Manuscript;
use App\Models\User;
use App\Models\Journal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class MonthlyReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Bulanan';

    protected static ?string $title = 'Laporan Bulanan';

    protected static string $view = 'filament.pages.monthly-report';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user?->hasRole('super_admin'), 403);

        $this->form->fill([
            'month' => now()->startOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Parameter Laporan')
                    ->description('Pilih bulan dan tahun untuk menghasilkan laporan.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('month')
                                    ->label('Bulan Laporan')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('F Y')
                                    ->format('Y-m-d'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('Unduh PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadReport'),
        ];
    }

    public function downloadReport()
    {
        $date = Carbon::parse($this->data['month']);
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $stats = $this->getStats($startDate, $endDate);
        
        $logoSetting = \App\Models\Setting::get('site_logo');
        $logoBase64 = null;
        
        if ($logoSetting) {
            $path = storage_path('app/public/' . $logoSetting);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('reports.monthly-report', [
            'month' => $date->format('F Y'),
            'stats' => $stats,
            'logo' => $logoBase64,
            'site_name' => \App\Models\Setting::get('site_name', config('app.name')),
            'generated_at' => now()->format('d M Y H:i'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Bulanan-' . $date->format('Y-m') . '.pdf');
    }

    protected function getStats($startDate, $endDate): array
    {
        return [
            'manuscripts' => [
                'submitted' => Manuscript::whereBetween('submitted_at', [$startDate, $endDate])->count(),
                'published' => Manuscript::whereBetween('published_at', [$startDate, $endDate])->count(),
                'rejected' => Manuscript::where('status', Manuscript::STATUS_REJECTED)
                    ->whereBetween('updated_at', [$startDate, $endDate])->count(),
            ],
            'users' => [
                'new' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            'journals' => Journal::withCount(['manuscripts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('submitted_at', [$startDate, $endDate]);
            }])->get(),
            'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
        ];
    }
}
