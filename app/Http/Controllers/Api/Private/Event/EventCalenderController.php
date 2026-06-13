<?php

namespace App\Http\Controllers\Api\Private\Event;


use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Requests\Ticket\TicketClient\CreateTicketClientRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\CreateSingleTicketClientAddressRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\UpdateSingleTicketClientAddressRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\CreateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\UpdateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\UpdateTicketClientRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\Event\AllEventResource;
use App\Http\Resources\Event\EventResource;
use App\Services\Event\EventService;
use App\Services\Ticket\TicketClient\TicketClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventCalenderController extends Controller
{
    private $eventService;
    private $ticketClientService;

    public function __construct(EventService $eventService, TicketClientService $ticketClientService)
    {
        $this->middleware('auth:api');
        $this->eventService = $eventService;
        $this->ticketClientService = $ticketClientService;
    }

    public function allevents(Request $request){

        $allEvents = $this->eventService->getAllEvents($request);

        return response()->json(
            AllEventResource::collection($allEvents)
        , 200);
    }

    public function create(CreateEventRequest $createEventReq, CreateTicketClientRequest $ticketClientReq, CreateSingleTicketClientContactRequest $ticketClientContactReq, CreateSingleTicketClientAddressRequest $ticketClientAddressReq){

        try {
            DB::beginTransaction();

            $createEventData = $createEventReq->validated();
            $ticketClientData = $ticketClientReq->validated();
            $ticketClientContactData = $ticketClientContactReq->validated();
            $ticketClientAddressData = $ticketClientAddressReq->validated();
    
            $ticketClientId = $ticketClientData['ticketClientId'];
    
            if($ticketClientData['ticketClientId'] === "0"){
    
                $ticketClientId = 0;
    
            } elseif($ticketClientData['ticketClientId'] === "" || $ticketClientData['ticketClientId'] === null) {
                
                $ticketClientId = $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);
    
            }
    
            $createEventData['ticketClientId'] = $ticketClientId;
                    
            $newEvent = $this->eventService->createEvent($createEventData);

            DB::commit();

            return response()->json([
                'message' => 'event has been created!'
            ], 200);
    

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function edit(Request $request){

        $event = $this->eventService->editEvent($request->eventId);

        return response()->json(
            new EventResource($event)
        , 200);

    }

    public function update(UpdateEventRequest $updateEventReq, UpdateTicketClientRequest $ticketClientReq, UpdateSingleTicketClientContactRequest $ticketClientContactReq, UpdateSingleTicketClientAddressRequest $ticketClientAddressReq){

        try {
            DB::beginTransaction();

            $createEventData = $updateEventReq->validated();
            $ticketClientData = $ticketClientReq->validated();
            $ticketClientContactData = $ticketClientContactReq->validated();
            $ticketClientAddressData = $ticketClientAddressReq->validated();
    
            $ticketClientId = $ticketClientData['ticketClientId'];
    
            if($ticketClientData['ticketClientId'] === "0"){
    
                $ticketClientId = 0;
    
            } elseif($ticketClientData['ticketClientId'] === "" || $ticketClientData['ticketClientId'] === null) {
                
                $ticketClientId = $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);
    
            } elseif($ticketClientData['ticketClientId'] > 0) {
                
                $ticketClientId = $this->ticketClientService->updateTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);
    
            }
    
            $createEventData['ticketClientId'] = $ticketClientId;
                    
            $event = $this->eventService->updateEvent($createEventData);

            DB::commit();

            return response()->json([
                'message' => 'event has been updated!'
            ], 200);
    

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }
    
    public function delete(Request $request){

        $event = $this->eventService->deleteEvent($request->eventId);

        return response()->json([
            "message" => "event has been deleted!"
        ], 200);

    }



}
