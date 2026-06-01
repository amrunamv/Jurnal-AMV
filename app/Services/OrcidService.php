<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class OrcidService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected bool $sandbox;
    protected string $apiUrl;
    protected string $oauthUrl;

    public function __construct()
    {
        $this->clientId = \App\Models\Setting::get('orcid_client_id', config('services.orcid.client_id', ''));
        $this->clientSecret = \App\Models\Setting::get('orcid_client_secret', config('services.orcid.client_secret', ''));
        $this->redirectUri = \App\Models\Setting::get('orcid_redirect', config('services.orcid.redirect', ''));
        
        $sandboxSetting = \App\Models\Setting::get('orcid_sandbox', config('services.orcid.sandbox', false));
        $this->sandbox = filter_var($sandboxSetting, FILTER_VALIDATE_BOOLEAN);
        
        $this->apiUrl = $this->sandbox 
            ? 'https://pub.sandbox.orcid.org/v3.0' 
            : 'https://pub.orcid.org/v3.0';
        
        $this->oauthUrl = $this->sandbox 
            ? 'https://sandbox.orcid.org/oauth' 
            : 'https://orcid.org/oauth';
    }

    /**
     * Get ORCID authorization URL
     */
    public function getAuthorizationUrl(?string $state = null): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => '/authenticate',
            'redirect_uri' => $this->redirectUri,
            'state' => $state ?? csrf_token(),
        ]);

        return "{$this->oauthUrl}/authorize?{$params}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeCode(string $code): ?array
    {
        try {
            $response = Http::asForm()->post("{$this->oauthUrl}/token", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    /**
     * Get ORCID profile by ID
     */
    public function getProfile(string $orcid, ?string $accessToken = null): ?array
    {
        try {
            $headers = ['Accept' => 'application/json'];
            
            if ($accessToken) {
                $headers['Authorization'] = "Bearer {$accessToken}";
            }

            $response = Http::withHeaders($headers)
                ->get("{$this->apiUrl}/{$orcid}/person");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    /**
     * Get employment history
     */
    public function getEmployments(string $orcid, ?string $accessToken = null): ?array
    {
        try {
            $headers = ['Accept' => 'application/json'];
            
            if ($accessToken) {
                $headers['Authorization'] = "Bearer {$accessToken}";
            }

            $response = Http::withHeaders($headers)
                ->get("{$this->apiUrl}/{$orcid}/employments");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    /**
     * Validate ORCID format
     */
    public function validateOrcid(string $orcid): bool
    {
        // ORCID format: 0000-0000-0000-000X (X can be digit or X)
        return (bool) preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $orcid);
    }

    /**
     * Extract ORCID from URL or string
     */
    public function extractOrcid(string $input): ?string
    {
        // Remove common ORCID URL prefixes
        $input = preg_replace('/^https?:\/\/(www\.)?(sandbox\.)?orcid\.org\//', '', $input);
        $input = trim($input);

        if ($this->validateOrcid($input)) {
            return $input;
        }

        return null;
    }

    /**
     * Link ORCID to user account
     */
    public function linkToUser(User $user, string $orcid): bool
    {
        if (!$this->validateOrcid($orcid)) {
            return false;
        }

        $user->update(['orcid' => $orcid]);
        return true;
    }

    /**
     * Get formatted name from ORCID profile
     */
    public function getNameFromProfile(array $profile): array
    {
        $name = $profile['name'] ?? [];
        
        return [
            'given_name' => $name['given-names']['value'] ?? '',
            'family_name' => $name['family-name']['value'] ?? '',
            'credit_name' => $name['credit-name']['value'] ?? null,
        ];
    }
}
