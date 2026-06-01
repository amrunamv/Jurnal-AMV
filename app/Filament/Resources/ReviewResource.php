<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ReviewResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('reviewer_id', Auth::id());
    }
    protected static ?string $model = Review::class;

    public static function getLabel(): string
    {
        return __('common.review');
    }

    public static function getPluralLabel(): string
    {
        return __('common.review');
    }

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Area Peninjauan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Manuskrip')
                    ->description('Tinjau detail sebelum memberikan penilaian.')
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\Group::make([
                                Forms\Components\Placeholder::make('manuscript_title')
                                    ->label('Judul Manuskrip')
                                    ->content(fn ($record) => $record?->manuscript?->title),
                                Forms\Components\Placeholder::make('manuscript_abstract')
                                    ->label('Abstrak')
                                    ->content(fn ($record) => $record?->manuscript?->abstract),
                            ])->grow(),
                            Forms\Components\Group::make([
                                Forms\Components\Placeholder::make('due_date_display')
                                    ->label('Tenggat Waktu')
                                    ->content(fn ($record) => $record?->due_date?->format('d M Y H:i') ?? 'N/A'),
                                Forms\Components\Placeholder::make('file_preview')
                                    ->label('File PDF')
                                    ->content(function ($record) {
                                        if (!$record?->manuscript) return 'No file';
                                        return new \Illuminate\Support\HtmlString('
                                            <a href="'.route('articles.download', $record->manuscript->slug).'" target="_blank" class="text-primary-600 font-bold flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                Unduh Manuskrip (PDF)
                                            </a>
                                        ');
                                    }),
                            ])->grow(false),
                        ]),
                    ])->collapsible(),

                Forms\Components\Section::make('Rubrik Penilaian')
                    ->description('Berikan komentar dan rekomendasi Anda.')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])

                            ->schema([
                                Forms\Components\Textarea::make('comments_to_author')
                                    ->label('Komentar untuk Penulis')
                                    ->required()
                                    ->rows(5),
                                Forms\Components\Textarea::make('comments_to_editor')
                                    ->label('Komentar untuk Editor (Rahasia)')
                                    ->required()
                                    ->rows(5),
                            ]),
                        Forms\Components\Select::make('recommendation')
                            ->label('Rekomendasi Akhir')
                            ->options([
                                'accept' => 'Terima (Tanpa Revisi)',
                                'minor' => 'Revisi Minor',
                                'major' => 'Revisi Mayor',
                                'reject' => 'Tolak',
                            ])
                            ->required()
                            ->native(false),
                    ]),
            ])->columns(['default' => 1]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manuscript.title')
                    ->label('Manuskrip')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Rekomendasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('round')
                    ->label('Putaran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Ditugaskan Pada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Tenggat Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Diterima Pada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai Pada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
