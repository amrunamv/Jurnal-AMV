<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManageApiSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 105;
    protected static string $view = 'filament.pages.manage-api-settings';

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationLabel(): string
    {
        return __('common.api_settings');
    }

    public function getTitle(): string
    {
        return __('common.api_settings');
    }

    public ?array $data = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user && $user->hasRole('super_admin'), 403);

        $this->form->fill([
            'crossref_username' => Setting::get('crossref_username'),
            'crossref_password' => Setting::get('crossref_password'),
            'crossref_doi_prefix' => Setting::get('crossref_doi_prefix', '10.00000'),
            'crossref_test_mode' => Setting::get('crossref_test_mode', true),
            
            'orcid_client_id' => Setting::get('orcid_client_id'),
            'orcid_client_secret' => Setting::get('orcid_client_secret'),
            'orcid_redirect' => Setting::get('orcid_redirect'),
            'orcid_sandbox' => Setting::get('orcid_sandbox', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('API Settings')
                    ->tabs([
                        Tabs\Tab::make('Crossref')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('crossref_username')
                                    ->label('Username / Email')
                                    ->email()
                                    ->required(),
                                TextInput::make('crossref_password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->revealable(),
                                TextInput::make('crossref_doi_prefix')
                                    ->label('DOI Prefix')
                                    ->placeholder('10.xxxxxx')
                                    ->required(),
                                Toggle::make('crossref_test_mode')
                                    ->label('Test Mode (Sandbox)')
                                    ->default(true),
                            ]),
                        Tabs\Tab::make('ORCID')
                            ->icon('heroicon-o-finger-print')
                            ->schema([
                                TextInput::make('orcid_client_id')
                                    ->label('Client ID')
                                    ->required(),
                                TextInput::make('orcid_client_secret')
                                    ->label('Client Secret')
                                    ->password()
                                    ->required()
                                    ->revealable(),
                                TextInput::make('orcid_redirect')
                                    ->label('Redirect URI')
                                    ->url()
                                    ->required(),
                                Toggle::make('orcid_sandbox')
                                    ->label('Sandbox Mode')
                                    ->default(false),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $group = str_starts_with($key, 'crossref') ? 'crossref' : 'orcid';
            Setting::set($key, $value, $group);
        }

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }
}
