<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 110;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Global';
    }

    public function getTitle(): string
    {
        return 'Pengaturan Global Situs';
    }

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user && $user->hasRole('super_admin'), 403);

        $this->form->fill([
            // General
            'site_name' => Setting::get('site_name', config('app.name')),
            'site_description' => Setting::get('site_description'),
            'site_logo' => Setting::get('site_logo'),
            'site_favicon' => Setting::get('site_favicon'),
            
            // Contact
            'contact_email' => Setting::get('contact_email'),
            'contact_email_2' => Setting::get('contact_email_2'),
            'contact_phone' => Setting::get('contact_phone'),
            'contact_address' => Setting::get('contact_address'),
            'google_maps_embed' => Setting::get('google_maps_embed'),

            // Social
            'social_facebook' => Setting::get('social_facebook'),
            'social_twitter' => Setting::get('social_twitter'),
            'social_instagram' => Setting::get('social_instagram'),
            'social_linkedin' => Setting::get('social_linkedin'),
            'social_youtube' => Setting::get('social_youtube'),

            // Footer
            'footer_about' => Setting::get('footer_about'),
            'footer_copyright' => Setting::get('footer_copyright'),

            // Page Contents
            'about_content' => Setting::get('about_content'),
            'contact_content' => Setting::get('contact_content'),
            'submission_guidelines_content' => Setting::get('submission_guidelines_content'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Umum')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nama Situs')
                                    ->required(),
                                Textarea::make('site_description')
                                    ->label('Deskripsi Situs')
                                    ->rows(3),
                                FileUpload::make('site_logo')
                                    ->label('Logo Situs')
                                    ->image()
                                    ->directory('settings'),
                                FileUpload::make('site_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('settings'),
                            ]),
                        
                        Tabs\Tab::make('Kontak')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label('Email Kontak Utama')
                                    ->email(),
                                TextInput::make('contact_email_2')
                                    ->label('Email Kontak Sekunder')
                                    ->email(),
                                TextInput::make('contact_phone')
                                    ->label('Nomor Telepon'),
                                Textarea::make('contact_address')
                                    ->label('Alamat Lengkap')
                                    ->rows(3),
                                Textarea::make('google_maps_embed')
                                    ->label('Embed Google Maps (HTML)')
                                    ->rows(3),
                            ]),

                        Tabs\Tab::make('Sosial Media')
                            ->icon('heroicon-m-share')
                            ->schema([
                                TextInput::make('social_facebook')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('social_twitter')
                                    ->label('Twitter / X URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('social_instagram')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('social_linkedin')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link'),
                                TextInput::make('social_youtube')
                                    ->label('YouTube URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link'),
                            ]),

                        Tabs\Tab::make('Footer')
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->schema([
                                Textarea::make('footer_about')
                                    ->label('Tentang di Footer')
                                    ->rows(3),
                                TextInput::make('footer_copyright')
                                    ->label('Teks Hak Cipta')
                                    ->placeholder('© 2024 AMV Open Science. All rights reserved.'),
                            ]),

                        Tabs\Tab::make('Konten Halaman')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                RichEditor::make('about_content')
                                    ->label('Konten Halaman Tentang')
                                    ->columnSpanFull(),
                                RichEditor::make('contact_content')
                                    ->label('Konten Halaman Kontak (Deskripsi)')
                                    ->columnSpanFull(),
                                RichEditor::make('submission_guidelines_content')
                                    ->label('Konten Panduan Pengajuan')
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'global');
        }

        Notification::make()
            ->title('Pengaturan Global Berhasil Disimpan')
            ->success()
            ->send();
    }
}
