<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    public static function getLabel(): string
    {
        return __('common.announcement');
    }

    public static function getPluralLabel(): string
    {
        return __('common.announcement');
    }

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make()
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\RichEditor::make('content')
                                    ->label('Konten')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h1',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ]),
                        Forms\Components\Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipe')
                                    ->options([
                                        'news' => 'Berita',
                                        'announcement' => 'Pengumuman',
                                        'event' => 'Acara',
                                        'call_for_papers' => 'Panggilan Makalah',
                                        'policy' => 'Perubahan Kebijakan',
                                        'conference' => 'Konferensi',
                                    ])
                                    ->required()
                                    ->default('news'),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Diterbitkan Pada')
                                    ->default(now()),
                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('Berakhir Pada'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                Forms\Components\Hidden::make('user_id')
                                    ->default(\Illuminate\Support\Facades\Auth::id()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'news' => 'Berita',
                        'announcement' => 'Pengumuman',
                        'event' => 'Acara',
                        'call_for_papers' => 'Panggilan Makalah',
                        'policy' => 'Kebijakan',
                        'conference' => 'Konferensi',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'news' => 'info',
                        'announcement' => 'gray',
                        'event' => 'success',
                        'call_for_papers' => 'success',
                        'policy' => 'warning',
                        'conference' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'news' => 'Berita',
                        'announcement' => 'Pengumuman',
                        'event' => 'Acara',
                        'call_for_papers' => 'Panggilan Makalah',
                        'policy' => 'Perubahan Kebijakan',
                        'conference' => 'Konferensi',
                    ]),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
