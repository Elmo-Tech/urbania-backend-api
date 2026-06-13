<?php

namespace App\Http\Controllers\Api\Private\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\CreateSingleAddressRequest;
use App\Http\Requests\Client\Address\UpdateSingleAddressRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AddressResource;
use App\Mail\ClientTicketEmail;
use App\Models\ClientContact;
use App\Models\Ticket;
use App\Models\TicketClientContact;
use App\Services\Client\AddressService;
use Illuminate\Http\Request;

class EmailByTicketController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function edit(Request $request){

        $ticket = Ticket::find($request->ticketId);

        $clientContact = TicketClientContact::where('ticket_client_id', $ticket->ticket_client_id)->first();

        return response()->json(['clientEmail' => $clientContact?->email??""], 200);

    }



}
