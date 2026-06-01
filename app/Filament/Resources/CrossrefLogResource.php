<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrossrefLogResource\Pages;
use App\Models\CrossrefLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CrossrefLogResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    protected static ?string $model = CrossrefLog::class;

    public static function getLabel(): string
    {
        return __('common.crossref_log');
    }

    public static function getPluralLabel(): string
    {
        return __('common.crossref_log');
    }

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manuscript_id')
                    ->label('Manuskrip')
                    ->relationship('manuscript', 'title')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('doi')
                    ->label('DOI')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('batch_id')
                    ->label('ID Batch')
                    ->disabled(),
                Forms\Components\KeyValue::make('request_payload')
                    ->label('Muatan Permintaan')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('response_payload')
                    ->label('Muatan Respon')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('error_message')
                    ->label('Pesan Kesalahan')
                    ->disabled()
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record?->error_message),
                Forms\Components\DateTimePicker::make('submitted_at')
                    ->label('Dikirim Pada')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('registered_at')
                    ->label('Terdaftar Pada')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('manuscript.title')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('doi')
                    ->label('DOI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'submitted' => 'info',
                        'registered' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'submitted' => 'Dikirim',
                        'registered' => 'Terdaftar',
                        'failed' => 'Gagal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'submitted' => 'Dikirim',
                        'registered' => 'Terdaftar',
                        'failed' => 'Gagal',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('check_status')
                    ->label('Cek Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->action(function (CrossrefLog $record) {
                        $service = app(\App\Services\CrossrefService::class);
                        $service->checkStatus($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Detail status diperbarui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrossrefLog $record) => $record->status === 'submitted'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCrossrefLogs::route('/'),
            // 'create' => Pages\CreateCrossrefLog::route('/create'),
            'view' => Pages\ViewCrossrefLog::route('/{record}'),
            // 'edit' => Pages\EditCrossrefLog::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
