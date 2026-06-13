<?php

namespace App\Http\Controllers;

use App\Services\Microsoft\MicrosoftGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MicrosoftCalendarController extends Controller
{
    // Get User Calendar Events
    public function getEvents()
    {
        $accessToken = MicrosoftGraphService::getAccessToken();

        //dd($accessToken);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0//me/calendar/events');

        /*$response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->get("https://graph.microsoft.com/v1.0/users/2100292@dhic.edu.eg/calendar/events");*/


        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch calendar events'], 500);
        }

        return response()->json($response->json());
    }

    // Create an Event
    public function createEvent(Request $request)
    {
        $accessToken = $request->header('Authorization');

        if (!$accessToken) {
            return response()->json(['error' => 'Access token is required'], 400);
        }

        $eventData = [
            'subject' => $request->input('subject'),
            'start' => [
                'dateTime' => $request->input('start_time'),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $request->input('end_time'),
                'timeZone' => 'UTC',
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://graph.microsoft.com/v1.0/me/events', $eventData);

        return response()->json($response->json());
    }
}
