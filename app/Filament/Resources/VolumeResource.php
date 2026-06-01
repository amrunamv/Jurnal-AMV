<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolumeResource\Pages;
use App\Filament\Resources\VolumeResource\RelationManagers;
use App\Models\Volume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VolumeResource extends Resource
{
    protected static ?string $model = Volume::class;

    public static function getLabel(): string
    {
        return __('common.volume');
    }

    public static function getPluralLabel(): string
    {
        return __('common.volume');
    }

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Manajemen Nomor';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('journal_id')
                    ->label(__('common.journal'))
                    ->relationship('journal', 'name')
                    ->required(),
                Forms\Components\TextInput::make('volume_number')
                    ->label(__('common.number'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('year')
                    ->label(__('common.year'))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('common.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('journal.name')
                    ->label(__('common.journal'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('volume_number')
                    ->label(__('common.number'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label(__('common.year')),
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
            'index' => Pages\ManageVolumes::route('/'),
        ];
    }
}
