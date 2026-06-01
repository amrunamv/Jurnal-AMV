<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalResource\Pages;
use App\Filament\Resources\JournalResource\RelationManagers;
use App\Models\Journal;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class JournalResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole(['super_admin', 'editor']) ?? false;
    }

    protected static ?string $model = Journal::class;

    public static function getLabel(): string
    {
        return __('common.journal');
    }

    public static function getPluralLabel(): string
    {
        return __('common.journal');
    }

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Pengaturan Sistem';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label(__('common.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(Journal::class, 'slug', ignoreRecord: true),
                Forms\Components\TextInput::make('issn')
                    ->label('ISSN')
                    ->maxLength(255),
                Forms\Components\TextInput::make('e_issn')
                    ->label('E-ISSN')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('common.description'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('publisher')
                    ->label(__('common.publisher'))
                    ->maxLength(255),
                Forms\Components\FileUpload::make('cover_image')
                    ->label(__('common.image'))
                    ->image()
                    ->directory('journal-covers')
                    ->visibility('public')
                    ->maxSize(2048),
                Forms\Components\TextInput::make('website_url')
                    ->label(__('common.website'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('metadata')
                    ->label('Metadata'),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('common.active'))
                    ->required(),
                Forms\Components\Section::make('Research Template')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('template')
                            ->collection('research_template')
                            ->label('Templat Riset (.DOCX)')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120)
                            ->downloadable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('e_issn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('website_url')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'edit' => Pages\EditJournal::route('/{record}/edit'),
        ];
    }
}
