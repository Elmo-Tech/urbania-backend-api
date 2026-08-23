<?php

namespace App\Services\ClientOuterTicket;

//use App\Http\Resources\Parameter\ParameterValueResource;
use App\Models\ClientOuterTicket;
use App\Models\Ticket;
use App\Models\TicketClient;
use App\Models\TicketClientAddress;
use App\Models\TicketClientContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ParameterValue;
use App\Models\Client;


class AiClientOuterTicketService{


    public function createClientOuterTicket(array $clientOuterTicketData){

        $cf = $clientOuterTicketData['cf']??$clientOuterTicketData['pIva']??null;
        if($cf != null){
            $ticketClient = TicketClient::where('national_number', $cf)->first();
        }
        
        $service = ParameterValue::where('parameter_value', $clientOuterTicketData['serviceName'])->first();
        $istanzaParameter = ParameterValue::where('parameter_value', $clientOuterTicketData['istanzaParameterName'])->first();
        $delegatedRole = ParameterValue::where('parameter_value', $clientOuterTicketData['delegatedRoleName'])->first();
        $client = Client::where('company_name', $clientOuterTicketData['clientName'])->where('urbania_client', 1)->first();

        $clientOuterTicket = ClientOuterTicket::create([
            "firstname" => $clientOuterTicketData['firstname']??'',
            "lastname" => $clientOuterTicketData['lastname']??'',
            "cf" => $clientOuterTicketData['cf']??'',
            'p_iva' => $clientOuterTicketData['pIva']??'',
            'ragione_sociale' => $clientOuterTicketData['ragioneSociale']??'',
            'delegated_firstname' => $clientOuterTicketData['delegatedFirstname']??'',
            'delegated_lastname' => $clientOuterTicketData['delegatedLastname']??'',
            'delegated_phone' => $clientOuterTicketData['delegatedPhone']??'',
            'message' => $clientOuterTicketData['message']??'', //messaggio (free text).
            "email" => $clientOuterTicketData['email']??'',
            'address' => $clientOuterTicketData['address']??'',
            'city' => $clientOuterTicketData['city']??null,
            'state' => $clientOuterTicketData['state']??null,
            "phone" => $clientOuterTicketData['phone']??'',
            'status' => $clientOuterTicketData['status']??0,
            'anno' => $clientOuterTicketData['anno']??'',
            'service_id' => $service->id ?? null, //motivo select will come from description or another field
            'istanza_parameter_id' => $istanzaParameter->id??null,
            'delegated_role_id' => $delegatedRole->id??null,
            'client_id' => $client->id??null,
            'email_token' => $clientOuterTicketData['emailToken']??'',
            'ticket_client_id' => $ticketClient->id??null
        ]);

        return $clientOuterTicket;

    }



}
