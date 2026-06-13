<?php

namespace App\Http\Controllers\Api\Private\TicketClient;

use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketClient\TicketClientService;
use Illuminate\Http\Request;

class TicketClientController extends Controller
{
    protected $ticketClientService;

    public function __construct(TicketClientService $ticketClientService)
    {
        $this->middleware('auth:api');
        $this->ticketClientService = $ticketClientService;
    }

        /**
     * Display a listing of the resource.
     */
    public function show(Request $request)
    {
        return $this->ticketClientService->editTicketClient($request->ticketClientId);
    }

}
