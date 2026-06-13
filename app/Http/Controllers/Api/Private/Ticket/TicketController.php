<?php

namespace App\Http\Controllers\Api\Private\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\TicketClient\CreateTicketClientRequest;
use App\Http\Requests\Ticket\TicketClient\UpdateTicketClientRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\CreateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\UpdateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\CreateSingleTicketClientAddressRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\UpdateSingleTicketClientAddressRequest;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->middleware('jwtauth');
        $this->ticketService = $ticketService;
    }

    public function alltickets(Request $request){
        return $this->ticketService->alltickets($request);
    }

    public function create(CreateTicketRequest $ticketReq, CreateTicketClientRequest $ticketClientReq, CreateSingleTicketClientContactRequest $ticketClientContactReq, CreateSingleTicketClientAddressRequest $ticketClientAddressReq){

        try {

        DB::beginTransaction();

        // Create the ticket
        $ticket = $this->ticketService->createTicket([...$ticketReq->validated()], $ticketClientReq->validated(), $ticketClientContactReq->validated(), $ticketClientAddressReq->validated());

        DB::commit();

        return response()->json([
            'message' => 'ticket has been created!'
        ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function edit(Request $request){

        return $this->ticketService->editTicket($request->ticketId);

    }

    public function update(UpdateTicketRequest $ticketReq, UpdateTicketClientRequest $ticketClientReq, UpdateSingleTicketClientContactRequest $ticketClientContactReq, UpdateSingleTicketClientAddressRequest $ticketClientAddressReq){

        return $this->ticketService->updateTicket($ticketReq->validated(), $ticketClientReq->validated(), $ticketClientContactReq->validated(), $ticketClientAddressReq->validated());

    }

    public function delete(Request $request){

        return $this->ticketService->deleteTicket($request->ticketId);

    }


}
