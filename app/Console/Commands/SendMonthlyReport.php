<?php

namespace App\Console\Commands;

use App\Mail\MonthlyReportMail;
use App\Models\Manuscript;
use App\Models\User;
use App\Models\Journal;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendMonthlyReport extends Command
{
    protected $signature = 'report:monthly';
    protected $description = 'Generate and send monthly activities report to Super Admins and Reviewers';

    public function handle()
    {
        $this->info('Starting Monthly Report generation...');

        // Period: Previous month
        $date = Carbon::now()->subMonth();
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $monthName = $date->format('F Y');

        $this->info("Generating report for: {$monthName}");

        $stats = $this->getStats($startDate, $endDate);
        $siteName = Setting::get('site_name', config('app.name'));
        
        // Logo retrieval logic (similar to MonthlyReport page)
        $logoSetting = Setting::get('site_logo');
        $logoBase64 = null;
        if ($logoSetting) {
            $path = storage_path('app/public/' . $logoSetting);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView('reports.monthly-report', [
            'month' => $monthName,
            'stats' => $stats,
            'logo' => $logoBase64,
            'site_name' => $siteName,
            'generated_at' => now()->format('d M Y H:i'),
        ]);
        
        $pdfOutput = $pdf->output();

        // Target Recipients: Super Admins and Reviewers
        $recipients = User::role(['super_admin', 'reviewer'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('No recipients found (super_admin or reviewer).');
            return;
        }

        foreach ($recipients as $user) {
            $this->info("Sending to: {$user->email} ({$user->name})");
            Mail::to($user->email)->send(new MonthlyReportMail($monthName, $pdfOutput, $siteName));
        }

        $this->info('Monthly Reports sent successfully!');
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
