<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Manuscript;
use App\Models\Journal;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) return [];

        if ($user->hasRole(['super_admin', 'editor'])) {
            return [
                Stat::make('Total Pengguna', User::count())
                    ->description('Pengguna terdaftar aktif')
                    ->descriptionIcon('heroicon-m-users')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('success'),
                
                Stat::make('Total Manuskrip', Manuscript::count())
                    ->description('Semua pengiriman')
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('primary'),

                Stat::make('Artikel Terbit', Manuscript::where('status', Manuscript::STATUS_PUBLISHED)->count())
                    ->description('Sudah dipublikasikan')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->color('success'),

                Stat::make('Menunggu Peninjauan', Manuscript::where('status', Manuscript::STATUS_UNDER_REVIEW)->count())
                    ->description('Sedang ditinjau mitra bestari')
                    ->descriptionIcon('heroicon-m-beaker')
                    ->color('warning'),

                Stat::make('Jurnal Aktif', Journal::where('is_active', true)->count())
                    ->description('Jurnal dalam sistem')
                    ->descriptionIcon('heroicon-m-building-library')
                    ->color('info'),

                Stat::make('Antrean Sistem', DB::table('jobs')->count())
                    ->description('Pekerjaan latar belakang')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color(DB::table('jobs')->count() > 10 ? 'danger' : 'gray'),
            ];
        }

        // Author Stats
        if ($user->hasRole('author')) {
            return [
                Stat::make('Manuskrip Saya', Manuscript::where('submitter_id', $user->id)->count())
                    ->description('Total pengiriman Anda')
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('primary'),

                Stat::make('Diterbitkan', Manuscript::where('submitter_id', $user->id)->where('status', Manuscript::STATUS_PUBLISHED)->count())
                    ->description('Artikel Anda yang sudah terbit')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->color('success'),

                Stat::make('Dalam Peninjauan', Manuscript::where('submitter_id', $user->id)->where('status', Manuscript::STATUS_UNDER_REVIEW)->count())
                    ->description('Artikel sedang dinilai')
                    ->descriptionIcon('heroicon-m-beaker')
                    ->color('warning'),
                
                Stat::make('Perlu Revisi', Manuscript::where('submitter_id', $user->id)->where('status', Manuscript::STATUS_REVISION_REQUIRED)->count())
                    ->description('Memerlukan tindakan Anda')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        return [];
    }
}
