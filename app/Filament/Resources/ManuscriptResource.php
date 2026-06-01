<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManuscriptResource\Pages;
use App\Filament\Resources\ManuscriptResource\RelationManagers;
use App\Models\Manuscript;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManuscriptResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = parent::getEloquentQuery();

        if ($user?->hasRole('super_admin')) {
            return $query;
        }

        if ($user?->hasRole(['editor', 'reviewer'])) {
            return $query;
        }

        if ($user?->hasRole('author')) {
            return $query->where('submitter_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }
    protected static ?string $model = Manuscript::class;

    public static function getLabel(): string
    {
        return __('common.manuscripts');
    }

    public static function getPluralLabel(): string
    {
        return __('common.manuscripts');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Meja Redaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Identitas & Penemuan')
                        ->description('Tentukan identitas inti dari riset Anda.')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Judul Manuskrip')
                                ->placeholder('Masukkan judul lengkap riset...')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('journal_id')
                                        ->label('Jurnal Target')
                                        ->relationship('journal', 'name')
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('issue_id')
                                        ->label('Nomor Target (Opsional)')
                                        ->relationship('issue', 'title')
                                        ->native(false)
                                        ->searchable()
                                        ->preload(),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Konten Riset')
                        ->description('Ringkaskan temuan ilmiah.')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->schema([
                            Forms\Components\Textarea::make('abstract')
                                ->label('Abstrak Ilmiah')
                                ->placeholder('Ringkasan komprehensif tentang tujuan, metodologi, dan hasil...')
                                ->required()
                                ->rows(8)
                                ->columnSpanFull(),
                                
                            Forms\Components\TagsInput::make('keywords')
                                ->label('Kata Kunci Pengindeksan')
                                ->placeholder('Tambah kata kunci dan tekan enter')
                                ->required()
                                ->helperText('Kata kunci ini akan digunakan untuk penemuan bertenaga AI.'),
                        ]),

                    Forms\Components\Wizard\Step::make('Repositori')
                        ->description('Unggah catatan ilmiah utama.')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->schema([
                            Forms\Components\FileUpload::make('manuscript_file')
                                ->label('Manuskrip Utama (PDF)')
                                ->helperText('Pastikan file telah dianonimkan untuk peninjauan buta jika diperlukan.')
                                ->disk('local')
                                ->directory('manuscripts')
                                ->visibility('private')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(10240)
                                ->downloadable()
                                ->required()
                                ->columnSpanFull(),
                            
                            Forms\Components\Placeholder::make('submission_note')
                                ->label('Protokol Pengiriman')
                                ->content('Dengan melanjutkan, Anda mengonfirmasi bahwa riset ini asli dan mengikuti pedoman etika.')
                        ]),
                ])->columnSpanFull()->persistStepInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Manuskrip')
                    ->searchable()
                    ->limit(50)
                    ->description(fn (Manuscript $record): string => $record->journal?->name ?? 'Tidak Ada Jurnal')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Manuscript::STATUS_DRAFT => 'gray',
                        Manuscript::STATUS_SUBMITTED => 'info',
                        Manuscript::STATUS_EDITOR_REVIEW => 'warning',
                        Manuscript::STATUS_UNDER_REVIEW => 'primary',
                        Manuscript::STATUS_REVISION_REQUIRED => 'danger',
                        Manuscript::STATUS_COPYEDITING => 'warning',
                        Manuscript::STATUS_PRODUCTION => 'success',
                        Manuscript::STATUS_PUBLISHED => 'success',
                        Manuscript::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Manuscript::STATUS_DRAFT => 'DRAF',
                        Manuscript::STATUS_SUBMITTED => 'DIKIRIM',
                        Manuscript::STATUS_EDITOR_REVIEW => 'PENGECEKAN EDITOR',
                        Manuscript::STATUS_UNDER_REVIEW => 'PENINJAUAN SEJAWAT',
                        Manuscript::STATUS_REVISION_REQUIRED => 'PERLU REVISI',
                        Manuscript::STATUS_COPYEDITING => 'PENYUNTINGAN',
                        Manuscript::STATUS_PRODUCTION => 'PRODUKSI',
                        Manuscript::STATUS_PUBLISHED => 'DITERBITKAN',
                        Manuscript::STATUS_REJECTED => 'DITOLAK',
                        default => strtoupper($state),
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        Manuscript::STATUS_PUBLISHED => 'heroicon-m-check-badge',
                        Manuscript::STATUS_UNDER_REVIEW => 'heroicon-m-beaker',
                        Manuscript::STATUS_REJECTED => 'heroicon-m-x-circle',
                        default => 'heroicon-m-academic-cap',
                    }),

                Tables\Columns\TextColumn::make('submitter.name')
                    ->label('Penulis Korespondensi')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label(__('common.reads'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Dikirim')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('register_doi')
                    ->label('Register DOI')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Register DOI with Crossref')
                    ->modalDescription('Apakah Anda yakin ingin mendaftarkan DOI untuk manuskrip ini? Ini akan mengirimkan metadata ke Crossref.')
                    ->action(fn (Manuscript $record) => $record->registerDoi())
                    ->visible(function (Manuscript $record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        return $user && 
                            $user->hasRole(['super_admin', 'editor']) && 
                            $record->status === Manuscript::STATUS_PUBLISHED && 
                            empty($record->doi);
                    }),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Manuscript $record) => route('articles.download', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(function (Manuscript $record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        return $user && (
                            $user->hasRole(['super_admin', 'editor']) || 
                            $user->id === $record->submitter_id
                        );
                    }),

                Tables\Actions\Action::make('download_loa')
                    ->label('Unduh LoA')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (Manuscript $record) {
                        // Placeholder for LoA generation (e.g., PDF export)
                        \Filament\Notifications\Notification::make()
                            ->title('Pengunduhan LoA Dimulai')
                            ->success()
                            ->send();
                        return response()->streamDownload(function () {
                            echo 'Letter of Acceptance content...';
                        }, 'LoA-' . $record->slug . '.txt');
                    })
                    ->visible(fn (Manuscript $record) => $record->status === 'accepted' && Auth::id() === $record->submitter_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContributorsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\CrossrefLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManuscripts::route('/'),
            'create' => Pages\CreateManuscript::route('/create'),
            'edit' => Pages\EditManuscript::route('/{record}/edit'),
        ];
    }
}
