<?php

namespace App\Filament\Widgets;

use App\Models\Manuscript;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestManuscripts extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected static ?string $heading = 'Manuskrip Terbaru';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Manuscript::query()->latest();

        if ($user && $user->hasRole('author')) {
            $query->where('submitter_id', $user->id);
        }

        return $table
            ->query($query->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Manuskrip')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),
                
                Tables\Columns\TextColumn::make('submitter.name')
                    ->label('Penulis')
                    ->visible(fn () => $user && $user->hasRole(['super_admin', 'editor'])),

                Tables\Columns\TextColumn::make('journal.name')
                    ->label('Jurnal'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Manuscript::STATUS_DRAFT => 'gray',
                        Manuscript::STATUS_PUBLISHED => 'success',
                        Manuscript::STATUS_REJECTED => 'danger',
                        Manuscript::STATUS_UNDER_REVIEW => 'primary',
                        Manuscript::STATUS_REVISION_REQUIRED => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Manuscript::STATUS_DRAFT => 'DRAF',
                        Manuscript::STATUS_SUBMITTED => 'DIKIRIM',
                        Manuscript::STATUS_UNDER_REVIEW => 'PENINJAUAN',
                        Manuscript::STATUS_PUBLISHED => 'TERBIT',
                        Manuscript::STATUS_REJECTED => 'DITOLAK',
                        default => strtoupper($state),
                    }),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tanggal Kirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->url(fn (Manuscript $record): string => route('filament.console.resources.manuscripts.edit', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
