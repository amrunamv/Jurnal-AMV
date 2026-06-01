<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueResource\Pages;
use App\Filament\Resources\IssueResource\RelationManagers;
use App\Models\Issue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    public static function getLabel(): string
    {
        return __('common.issue');
    }

    public static function getPluralLabel(): string
    {
        return __('common.issue');
    }

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Manajemen Nomor';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('volume_id')
                    ->label(__('common.volume'))
                    ->relationship('volume', 'id')
                    ->required(),
                Forms\Components\TextInput::make('issue_number')
                    ->label(__('common.number'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->label(__('common.title'))
                    ->maxLength(255),
                Forms\Components\DatePicker::make('publication_date')
                    ->label(__('common.date')),
                Forms\Components\Textarea::make('description')
                    ->label(__('common.description'))
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image')
                    ->label(__('common.image'))
                    ->image(),
                Forms\Components\Toggle::make('is_published')
                    ->label(__('common.published'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('volume.id')
                    ->label(__('common.volume'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_number')
                    ->label(__('common.number'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('common.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('publication_date')
                    ->label(__('common.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label(__('common.image')),
                Tables\Columns\IconColumn::make('is_published')
                    ->label(__('common.published'))
                    ->boolean(),
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
            'index' => Pages\ManageIssues::route('/'),
        ];
    }
}
