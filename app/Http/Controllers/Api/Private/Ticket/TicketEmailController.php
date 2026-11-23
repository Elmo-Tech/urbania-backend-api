<?php

namespace App\Http\Controllers\Api\Private\Ticket;

use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:updateTicket,api');
    }

    public function resend(Request $request, TicketEmailService $emails): JsonResponse
    {
        $data = $request->validate([
            'ticketId' => 'required|integer|min:1',
        ]);

        return response()->json($emails->resendAcceptance((int) $data['ticketId'], (int) $request->user()->id));
    }
}
