<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManageJournalSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 90;
    protected static string $view = 'filament.pages.manage-journal-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('common.journal_settings');
    }

    public function getTitle(): string
    {
        return __('common.journal_settings');
    }

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user && $user->hasRole('super_admin'), 403);

        $this->form->fill([
            'journal_name' => Setting::get('journal_name', config('app.name')),
            'journal_description' => Setting::get('journal_description'),
            'journal_logo' => Setting::get('journal_logo'),
            'journal_favicon' => Setting::get('journal_favicon'),
            'contact_email' => Setting::get('contact_email'),
            'contact_phone' => Setting::get('contact_phone'),
            'office_address' => Setting::get('office_address'),
            'research_template' => Setting::get('research_template'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->description('Nama dan deskripsi utama jurnal.')
                    ->schema([
                        TextInput::make('journal_name')
                            ->label('Nama Jurnal')
                            ->required(),
                        Textarea::make('journal_description')
                            ->label('Deskripsi / Tentang Jurnal')
                            ->rows(3),
                    ]),

                Section::make('Identitas Visual')
                    ->description('Logo dan favicon untuk branding.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('journal_logo')
                            ->label('Logo Jurnal')
                            ->image()
                            ->directory('settings'),
                        FileUpload::make('journal_favicon')
                            ->label('Favicon')
                            ->image()
                            ->directory('settings'),
                    ]),

                Section::make('Informasi Kontak')
                    ->description('Detail kontak resmi jurnal.')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email Kontak')
                            ->email(),
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon'),
                        Textarea::make('office_address')
                            ->label('Alamat Kantor')
                            ->rows(2),
                    ]),

                Section::make('Templat Manuskrip')
                    ->description('Upload templat riset (.DOCX) yang akan digunakan penulis untuk pengiriman manuskrip. File ini bisa diunduh di halaman Panduan Pengiriman.')
                    ->schema([
                        FileUpload::make('research_template')
                            ->label('Templat Riset (.DOCX)')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword',
                            ])
                            ->directory('templates')
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->helperText('Format yang diterima: .docx, .doc. Maksimal 10MB.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'journal');
        }

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();
    }
}
