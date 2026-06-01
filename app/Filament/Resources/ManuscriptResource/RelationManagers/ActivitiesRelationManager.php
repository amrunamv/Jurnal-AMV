<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelaku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi Protokol')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('from_status')
                    ->label('Fase Awal')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('to_status')
                    ->label('Fase Tujuan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Catatan')
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only log
            ])
            ->actions([
                // Read-only log
            ])
            ->bulkActions([
                // Read-only log
            ]);
    }
}
