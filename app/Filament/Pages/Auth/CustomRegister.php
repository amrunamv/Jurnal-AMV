<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;

    use Filament\Forms\Components\Checkbox;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\Wizard;
    use Filament\Forms\Form;
    use Filament\Pages\Auth\Register;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rules\Password;

class CustomRegister extends Register
{
    public function form(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Account')
                        ->schema([
                            TextInput::make('username')
                                ->label('Username')
                                ->required()
                                ->unique(table: User::class)
                                ->maxLength(50)
                                ->alphaDash(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                    
                    Wizard\Step::make('Identity')
                        ->schema([
                            Select::make('salutation')
                                ->options([
                                    'Prof.' => 'Prof.',
                                    'Dr.' => 'Dr.',
                                    'Mr.' => 'Mr.',
                                    'Ms.' => 'Ms.',
                                    'Mrs.' => 'Mrs.',
                                ])
                                ->native(false),
                            TextInput::make('first_name')
                                ->label('First Name')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('middle_name')
                                ->label('Middle Name')
                                ->maxLength(50),
                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('orcid')
                                ->label('ORCID iD')
                                ->placeholder('0000-0000-0000-0000')
                                ->mask('9999-9999-9999-999*')
                                ->regex('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/')
                                ->unique(table: User::class)
                                ->helperText('Format: 0000-0000-0000-0000'),
                            TextInput::make('scopus_id')
                                ->label('Scopus ID')
                                ->maxLength(50),
                        ]),

                    Wizard\Step::make('Profile')
                        ->schema([
                            Select::make('affiliation_id')
                                ->label('Affiliation')
                                ->relationship('affiliation', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('city')
                                        ->maxLength(255),
                                    TextInput::make('country')
                                        ->maxLength(255),
                                ])
                                ->required(),
                            Select::make('country_code')
                                ->label('Country')
                                ->options([
                                    'ID' => 'Indonesia',
                                    'US' => 'United States',
                                    'GB' => 'United Kingdom',
                                    'AU' => 'Australia',
                                    'JP' => 'Japan',
                                    // Add more as needed
                                ])
                                ->searchable(),
                            TextInput::make('bio_statement')
                                ->label('Short Bio')
                                ->maxLength(1000),
                            Checkbox::make('is_reviewer_candidate')
                                ->label('I would like to be a Reviewer')
                                ->helperText('Your application will be reviewed by the Editor-in-Chief.'),
                        ]),
                ])->submitAction(new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render(<<<BLADE
                    <x-filament::button type="submit" size="sm">
                        Register
                    </x-filament::button>
                BLADE))),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return \DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'name' => "{$data['first_name']} {$data['last_name']}", // Backward compatibility
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'salutation' => $data['salutation'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'orcid' => $data['orcid'] ?? null,
                'scopus_id' => $data['scopus_id'] ?? null,
                'affiliation_id' => $data['affiliation_id'],
                'country_code' => $data['country_code'] ?? null,
                'bio_statement' => $data['bio_statement'] ?? null,
                'is_reviewer_candidate' => $data['is_reviewer_candidate'] ?? false,
            ]);

            // Auto-assign 'author' role
            $user->assignRole('author');

            return $user;
        });
    }
}
