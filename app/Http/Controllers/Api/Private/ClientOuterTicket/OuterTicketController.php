<?php

namespace App\Http\Controllers\Api\Private\ClientOuterTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientOuterTicket\UpdateClientOuterTicketRequest;
use App\Http\Resources\ClientOuterTicket\AllClientOuterTicketCollection;
use App\Http\Resources\ClientOuterTicket\ClientOuterTicketResource;
use App\Mail\OuterTicketCreated;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Models\TicketClientContact;
use App\Services\ClientOuterTicket\ClientOuterTicketService;
use App\Utils\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\Ticket\TicketService;

class OuterTicketController extends Controller
{
    protected $clientOuterTicketService;
    protected $ticketService;

    public function __construct(ClientOuterTicketService $clientOuterTicketService, TicketService $ticketService)
    {
        $this->middleware('auth:api');
        $this->clientOuterTicketService = $clientOuterTicketService;
        $this->ticketService = $ticketService;
    }

    public function index(Request $request){


        $allClientOuterTickets = $this->clientOuterTicketService->allClientOuterTickets($request->all());


        return response()->json(
            new AllClientOuterTicketCollection(PaginateCollection::paginate($allClientOuterTickets, $request->pageSize?$request->pageSize:10))
        );
    }


    public function edit(Request $request)
    {
        $clientOuterTicketSchedule = $this->clientOuterTicketService->editClientOuterTicket($request->clientOuterTicketId);

        return response()->json(new ClientOuterTicketResource($clientOuterTicketSchedule));
    }

    public function update(UpdateClientOuterTicketRequest $updateClientOuterTicketRequest)
    {
        try {
            DB::beginTransaction();
            $clientOuterTicket = $this->clientOuterTicketService->updateClientOuterTicket($updateClientOuterTicketRequest->validated());

            if($clientOuterTicket->accept_status == 1){
                $ticket = Ticket::create([
                    'client_id' => $clientOuterTicket->client_id,
                    'contract_id' => $clientOuterTicket->contract_id,
                    'contract_id_2' => $clientOuterTicket->contract_two_id,
                    'service_id' => $clientOuterTicket->service_id,
                    'worker_id' => $clientOuterTicket->worker_id,//
                    'ticket_client_id' => $clientOuterTicket->ticket_client_id,
                    'ticket_number' => "",//
                    'notify_date' => $clientOuterTicket->notify_date,
                    //'after_notify_date' => $clientOuterTicket->after_notify_date,
                    'status' => $clientOuterTicket->status,
                    'closer_id' => $clientOuterTicket->closer_id,
                    'end_date' => $clientOuterTicket->end_date,
                    'connect_type_id' => $clientOuterTicket->connect_type_id,
                    'description' => $clientOuterTicket->message,
                    'esito' => $clientOuterTicket->esito,
                    'note' => $clientOuterTicket->note,
                    'status_date' => $clientOuterTicket->status_date,
                    'anno' => $clientOuterTicket->anno,
                    'tipologia_istanza' => $clientOuterTicket->istanza_parameter_id,
                    'segnalazione' => $clientOuterTicket->segnalazione,
                    'urgenza' => $clientOuterTicket->urgenza
                ]);
                $ticket->email_token = bin2hex(random_bytes(16));
                $ticket->save();
                $ticketClient = TicketClientContact::where('ticket_client_id', $ticket->ticket_client_id)->first();
                
                if($ticketClient?->email){
                    Mail::to($ticketClient->email)->send(new OuterTicketCreated($ticket));
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'clientOuterTicket has been updated !',
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }


    public function destroy(Request $request)
    {
        $this->clientOuterTicketService->deleteClientOuterTicket($request->clientOuterTicketId);

        return response()->json([
            'message' => 'clientOuterTicket has behen deleted !',
        ]);
    }

}
