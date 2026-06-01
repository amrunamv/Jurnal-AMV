<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContributorsRelationManager extends RelationManager
{
    protected static string $relationship = 'contributors';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Biografis')
                    ->description('Identitas ilmiah dan detail afiliasi.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('given_name')
                                    ->label('Nama Depan')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('family_name')
                                    ->label('Nama Belakang')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Institusi')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('orcid')
                                    ->label('ID ORCID')
                                    ->placeholder('0000-0000-0000-0000')
                                    ->maxLength(255)
                                    ->rules(['nullable', 'regex:/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/']),
                            ]),
                        
                        Forms\Components\Select::make('affiliation_id')
                            ->label('Institusi Utama')
                            ->relationship('affiliation', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Peran Riset')
                    ->description('Posisi dan tanggung jawab dalam manuskrip ini.')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('role')
                                    ->label('Peran Ilmiah')
                                    ->placeholder('Misal: Peneliti Utama')
                                    ->default('author')
                                    ->required(),
                                Forms\Components\TextInput::make('order')
                                    ->label('Urutan Penulis')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\Toggle::make('is_corresponding')
                                    ->label('Penulis Korespondensi')
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('given_name')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name') // Using accessor from model
                    ->label(__('common.name'))
                    ->searchable(['given_name', 'family_name']),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('orcid')
                    ->label('ID ORCID')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_corresponding')
                    ->boolean()
                    ->label('Kor.'),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
