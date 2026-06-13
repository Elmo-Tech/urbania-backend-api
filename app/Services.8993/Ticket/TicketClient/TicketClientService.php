<?php

namespace App\Services\Ticket\TicketClient;

use App\Http\Resources\Ticket\TicketClientResource;
use App\Models\Ticket;
use App\Models\TicketClient;
use App\Services\Ticket\TicketClient\TicketClientContacts\TicketClientContactService;
use App\Services\Ticket\TicketClient\TicketClientAddresses\TicketClientAddressService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TicketClientService{

    protected $ticketClientContactService;
    protected $ticketClientAddressService;

    public function __construct(TicketClientContactService $ticketClientContactService , TicketClientAddressService $ticketClientAddressService)
    {
        $this->ticketClientContactService = $ticketClientContactService;
        $this->ticketClientAddressService = $ticketClientAddressService;
    }


    public function createTicketClient(array $ticketClientData, array $ticketClientContactData, array $ticketClientAddressData): int{
    
        //dd($ticketClientData);
        $ticketClient = TicketClient::create([
            'firstname'=> $ticketClientData['firstname'],
            'lastname'=> $ticketClientData['lastname'],
            'company_name'=> $ticketClientData['companyName'],
            'national_number'=> $ticketClientData['nationalNumber'],
        ]);

        if(isset($ticketClientContactData) && count($ticketClientContactData) > 0){
            $this->ticketClientContactService->createTicketClientSingleContact($ticketClient->id, $ticketClientContactData);

        }
        
        if(isset($ticketClientAddressData) && count($ticketClientAddressData) > 0){
            $this->ticketClientAddressService->createTicketClientSingleAddress($ticketClient->id, $ticketClientAddressData);

        }
        
        return $ticketClient->id;

    }

    public function editTicketClient(int $ticketClientId){
        $ticketClient = TicketClient::query()->where('id', '=', $ticketClientId)->with('address')->with('contact')->get();
            
        return response()->json(
            TicketClientResource::collection($ticketClient)[0]
        , 200);

    }

    public function updateTicketClient(array $ticketClientData, array $ticketClientContactData, array $ticketClientAddressData): int{
    
        //dd($ticketClientData);
        $ticketClient = TicketClient::find($ticketClientData['ticketClientId']);
        $ticketClient->fill([
            'firstname'=> $ticketClientData['firstname'],
            'lastname'=> $ticketClientData['lastname'],
            'company_name'=> $ticketClientData['companyName'],
            'national_number'=> $ticketClientData['nationalNumber'],
        ]);

        $ticketClient->save();

        if(isset($ticketClientContactData) && count($ticketClientContactData) > 0){
            $this->ticketClientContactService->updateTicketClientSingleContact($ticketClientContactData);

        }
        
        if(isset($ticketClientAddressData) && count($ticketClientAddressData) > 0){
            $this->ticketClientAddressService->updateTicketClientSingleAddress($ticketClientAddressData);

        }
        
        return $ticketClient->id;

    }

   
}