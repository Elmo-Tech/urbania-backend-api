<?php

namespace App\Services\Microsoft;

use Illuminate\Support\Facades\Http;

class MicrosoftGraphService
{
    public static function getAccessToken()
    {
        $response = Http::asForm()->post(env('MICROSOFT_TOKEN_URL'), [
            'client_id' => env('MICROSOFT_CLIENT_ID'),
            'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
            'scope' => env('MICROSOFT_SCOPE'),
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to obtain Microsoft access token: ' . $response->body());
        }
        return $response->json()['access_token'];
    }
}
