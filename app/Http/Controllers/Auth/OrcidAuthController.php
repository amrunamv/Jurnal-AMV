<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OrcidService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class OrcidAuthController extends Controller
{
    public function __construct(
        protected OrcidService $orcidService
    ) {}

    /**
     * Redirect to ORCID authorization page
     */
    public function redirect()
    {
        return redirect()->away($this->orcidService->getAuthorizationUrl());
    }

    /**
     * Handle ORCID callback
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            Notification::make()
                ->title('ORCID Authentication Failed')
                ->body($request->get('error_description', 'Authentication were cancelled or failed.'))
                ->danger()
                ->send();
            
            return redirect()->route('filament.console.auth.login');
        }

        $code = $request->get('code');

        if (!$code) {
            Notification::make()
                ->title('ORCID Authentication Failed')
                ->body('Authorization code missing from ORCID.')
                ->danger()
                ->send();

            return redirect()->route('filament.console.auth.login');
        }

        $tokenData = $this->orcidService->exchangeCode($code);

        if (!$tokenData || !isset($tokenData['orcid'])) {
            Notification::make()
                ->title('ORCID Authentication Failed')
                ->body('Unable to retrieve access token from ORCID.')
                ->danger()
                ->send();

            return redirect()->route('filament.console.auth.login');
        }

        $orcid = $tokenData['orcid'];

        // Case 1: User is already logged in -> Link the account
        if (Auth::check()) {
            $user = Auth::user();
            if ($this->orcidService->linkToUser($user, $orcid)) {
                Notification::make()
                    ->title('ORCID Linked')
                    ->success()
                    ->send();
            }
            return redirect()->intended('/console');
        }

        // Case 2: User is not logged in -> Attempt Login
        $user = User::where('orcid', $orcid)->first();

        if ($user) {
            Auth::login($user);
            Notification::make()
                ->title('Logged in via ORCID')
                ->success()
                ->send();
            return redirect()->intended('/console');
        }

        // Case 3: User not found -> Redirect to Register with ORCID
        Notification::make()
            ->title('ORCID Authenticated')
            ->body('No account found with this ORCID. Please complete registration.')
            ->info()
            ->send();

        // Pass ORCID data to registration page via session
        session(['orcid_data' => [
            'orcid' => $orcid,
            'name' => $tokenData['name'] ?? null,
        ]]);

        return redirect()->route('filament.console.auth.register');
    }
}
