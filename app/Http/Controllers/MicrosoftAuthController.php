<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MicrosoftAuthController extends Controller
{
    // Step 1: Redirect to Microsoft OAuth
    public function redirectToMicrosoft()
    {
        $queryParams = [
            'client_id' => env('MICROSOFT_CLIENT_ID'),
            'response_type' => 'code',
            'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
            'response_mode' => 'query',
            'scope' => env('MICROSOFT_SCOPE'),
        ];

        return redirect('https://login.microsoftonline.com/' . env('MICROSOFT_TENANT_ID') . '/oauth2/v2.0/authorize?' . http_build_query($queryParams));
    }

    // Step 2: Handle Callback & Get Access Token
    public function handleMicrosoftCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code missing'], 400);
        }

        $tokenResponse = Http::asForm()->post('https://login.microsoftonline.com/' . env('MICROSOFT_TENANT_ID') . '/oauth2/v2.0/token', [
            'client_id' => env('MICROSOFT_CLIENT_ID'),
            'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
            'code' => $code,
            'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
        ]);

        $tokenData = $tokenResponse->json();

        if (!isset($tokenData['access_token'])) {
            return response()->json(['error' => 'Failed to get access token'], 400);
        }

        return response()->json([
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'expires_in' => $tokenData['expires_in'],
        ]);
    }
}
