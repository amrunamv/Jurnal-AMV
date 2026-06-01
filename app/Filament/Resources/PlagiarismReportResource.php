<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlagiarismReportResource\Pages;
use App\Filament\Resources\PlagiarismReportResource\RelationManagers;
use App\Models\PlagiarismReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlagiarismReportResource extends Resource
{
    protected static ?string $model = PlagiarismReport::class;

    public static function getLabel(): string
    {
        return __('common.plagiarism_report');
    }

    public static function getPluralLabel(): string
    {
        return __('common.plagiarism_report');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationGroup = 'Meja Redaksi';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manuscript_id')
                    ->label(__('common.manuscripts'))
                    ->relationship('manuscript', 'title')
                    ->required(),
                Forms\Components\Select::make('revision_id')
                    ->label('Revisi')
                    ->relationship('revision', 'id'),
                Forms\Components\TextInput::make('similarity_score')
                    ->label('Skor Kemiripan')
                    ->numeric(),
                Forms\Components\TextInput::make('provider')
                    ->label('Penyedia')
                    ->required()
                    ->maxLength(255)
                    ->default('turnitin'),
                Forms\Components\TextInput::make('report_url')
                    ->label('URL Laporan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('certificate_file')
                    ->label('File Sertifikat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('details')
                    ->label('Detail'),
                Forms\Components\TextInput::make('checked_by')
                    ->label('Dicek Oleh')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('checked_at')
                    ->label('Dicek Pada'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manuscript.title')
                    ->label('Manuskrip')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('revision.id')
                    ->label('Revisi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('similarity_score')
                    ->label('Skor Kemiripan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Penyedia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('certificate_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('checked_by')
                    ->label('Dicek Oleh')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Dicek Pada')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePlagiarismReports::route('/'),
        ];
    }
}
