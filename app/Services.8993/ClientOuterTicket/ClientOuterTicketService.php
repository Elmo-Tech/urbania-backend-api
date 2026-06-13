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

class ClientOuterTicketService{

    public function allClientOuterTickets(array $request){

        $clientOuterTickets = ClientOuterTicket::with('client')->get();

        return $clientOuterTickets;
    }

    public function createClientOuterTicket(array $clientOuterTicketData){

        //$newResevationSchedule = ResevationSchedule::find($parameterData['parameterId']);
        $cf = $clientOuterTicketData['cf']??$clientOuterTicketData['pIva']??null;
        if($cf != null){
            $ticketClient = TicketClient::where('national_number', $cf)->first();
        }

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
            "phone" => $clientOuterTicketData['phone']??'',
            'status' => $clientOuterTicketData['status']??0,
            'anno' => $clientOuterTicketData['anno']??'',
            'service_id' => $clientOuterTicketData['serviceId']??null, //motivo select will come from description or another field
            'istanza_parameter_id' => $clientOuterTicketData['istanzaParameterId']??null,
            'delegated_role_id' => $clientOuterTicketData['delegatedRoleId']??null,
            'client_id' => $clientOuterTicketData['clientId']??null,
            'email_token' => $clientOuterTicketData['emailToken']??'',
            'ticket_client_id' => $ticketClient->id??null
        ]);

        return $clientOuterTicket;

    }

    public function editClientOuterTicket(int $clientOuterTicketId){

        $clientOuterTicket = ClientOuterTicket::find($clientOuterTicketId);

        return $clientOuterTicket;

    }

    public function updateClientOuterTicket(array $clientOuterTicketData): mixed{

        $clientOuterTicket = ClientOuterTicket::find($clientOuterTicketData['clientOuterTicketId']);

        $clientOuterTicket->firstname = $clientOuterTicketData['firstname'] ?? '';
        $clientOuterTicket->lastname = $clientOuterTicketData['lastname'] ?? '';
        $clientOuterTicket->cf = $clientOuterTicketData['cf'] ?? '';
        $clientOuterTicket->p_iva = $clientOuterTicketData['pIva'] ?? '';
        $clientOuterTicket->ragione_sociale = $clientOuterTicketData['ragioneSociale'] ?? '';
        $clientOuterTicket->delegated_firstname = $clientOuterTicketData['delegatedFirstname'] ?? '';
        $clientOuterTicket->delegated_lastname = $clientOuterTicketData['delegatedLastname'] ?? '';
        $clientOuterTicket->delegated_phone = $clientOuterTicketData['delegatedPhone'] ?? '';
        $clientOuterTicket->message = $clientOuterTicketData['message'] ?? '';
        $clientOuterTicket->email = $clientOuterTicketData['email'] ?? '';
        $clientOuterTicket->address = $clientOuterTicketData['address'] ?? '';
        $clientOuterTicket->phone = $clientOuterTicketData['phone'] ?? '';
        $clientOuterTicket->status = $clientOuterTicketData['status'] ?? 0;
        $clientOuterTicket->anno = $clientOuterTicketData['anno'] ?? '';
        $clientOuterTicket->service_id = $clientOuterTicketData['serviceId'] ?? null;
        $clientOuterTicket->istanza_parameter_id = $clientOuterTicketData['istanzaParameterId'] ?? null;
        $clientOuterTicket->delegated_role_id = $clientOuterTicketData['delegatedRoleId'] ?? null;
        $clientOuterTicket->client_id = $clientOuterTicketData['clientId'] ?? null;
        $clientOuterTicket->status_date = $clientOuterTicketData['status'] == $clientOuterTicket->status ? $clientOuterTicket->status_date : Carbon::now();


        $contract = explode("##", $clientOuterTicketData['contractId']);

        $parameterValue = DB::table('parameter_values')->select('description')->where('description', "0")->first();

        if($clientOuterTicketData['urgenza'] != null){
            $parameterValue = DB::table('parameter_values')->select('description')->where('id', $clientOuterTicketData['urgenza'])->first();
        }

        if ( ($clientOuterTicket->status != "2" && $clientOuterTicketData['status'] == "2") || $clientOuterTicket->status != "3" && $clientOuterTicketData['status'] == "3") {
            $clientOuterTicket->closer_id = Auth::id();
        }


        if($clientOuterTicketData['status'] == 1 && $clientOuterTicket->email_token == null){
            $clientOuterTicket->email_token = str::random(40);
            $afterNotifyDate = null;
            if($clientOuterTicketData['notifyDate'] != null){
                $date = Carbon::parse($clientOuterTicketData['notifyDate']);
                $afterNotifyDate = $date->addDays(60);
            }

            $clientOuterTicket->notify_date = $afterNotifyDate;
        }

        $ticketClientId = null;

        if($clientOuterTicketData['ticketClientId'] === "0"){

            $ticketClientId = null;

        } elseif($clientOuterTicketData['ticketClientId'] === "" || $clientOuterTicket->ticketClientId === null) {

            $ticketClient = TicketClient::create([
                'firstname'=> $clientOuterTicketData['firstname'],
                'lastname'=> $clientOuterTicketData['lastname'],
                'company_name'=> $clientOuterTicketData['ragioneSociale'],
                'national_number'=> $clientOuterTicketData['cf']??$ticketClientData['pIva']??null,
            ]);

            $ticketClientContact = TicketClientContact::create([
                'phone_number'=> $clientOuterTicketData['phone'],
                'email'=> $clientOuterTicketData['email'],
                'ticket_client_id' => $ticketClient->id
            ]);

            $ticketClientAddress = TicketClientAddress::create([
                'address'=> $clientOuterTicketData['address'],
                'city' => $clientOuterTicketData['city']??null,
                'state' => $clientOuterTicketData['state']??null,
                'postal_code' => $clientOuterTicketData['postalCode']??null,
                'ticket_client_id' => $ticketClient->id
            ]);

            $ticketClientId = $ticketClient->id;

        } elseif($clientOuterTicketData['ticketClientId'] >= 1){

            $ticketClientId = $clientOuterTicketData['ticketClientId'];

            $ticketClient = TicketClient::find($clientOuterTicketData['ticketClientId']);

            $ticketClient->firstname = $clientOuterTicketData['firstname'];
            $ticketClient->lastname = $clientOuterTicketData['lastname'];
            $ticketClient->company_name = $clientOuterTicketData['ragioneSociale'];
            $ticketClient->national_number = $clientOuterTicketData['cf']??$ticketClientData['pIva']??null;
            $ticketClient->save();

            $ticketClientContact = TicketClientContact::where('ticket_client_id', $clientOuterTicketData['ticketClientId'])->first();

            $ticketClientContact->phone_number = $clientOuterTicketData['phone'];
            $ticketClientContact->email = $clientOuterTicketData['email'];
            $ticketClientContact->save();

            $ticketClientAddress = TicketClientAddress::where('ticket_client_id', $clientOuterTicketData['ticketClientId'])->first();

            $ticketClientAddress->address = $clientOuterTicketData['address'];
            $ticketClientAddress->city = $clientOuterTicketData['city']??null;
            $ticketClientAddress->state = $clientOuterTicketData['state']??null;
            $ticketClientAddress->postal_code = $clientOuterTicketData['postalCode']??null;
            $ticketClientAddress->save();

        }

        $clientOuterTicket->contract_id = $contract[0];
        $clientOuterTicket->contract_two_id = $contract[1];
        $clientOuterTicket->ticket_client_id = $ticketClientId;
        $clientOuterTicket->notify_date = $clientOuterTicketData['notifyDate'] ?? null;
        $clientOuterTicket->end_date = $clientOuterTicketData['endDate'] ?? null;
        $clientOuterTicket->connect_type_id = $clientOuterTicketData['connectTypeId'] ?? null;
        $clientOuterTicket->esito = $clientOuterTicketData['esito'] ?? null;
        $clientOuterTicket->note = $clientOuterTicketData['note'] ?? null;
        $clientOuterTicket->segnalazione = $clientOuterTicketData['segnalazione'] ?? null;
        $clientOuterTicket->urgenza = $parameterValue->description;
        $clientOuterTicket->accept_status = $clientOuterTicketData['acceptStatus'] ?? 0;
        $clientOuterTicket->worker_id = $clientOuterTicketData['workerId'] ?? null;

        $clientOuterTicket->save();

        return $clientOuterTicket;

    }

    public function deleteClientOuterTicket(int $clientOuterTicketId){
        $clientOuterTicket = ClientOuterTicket::find($clientOuterTicketId);
        $clientOuterTicket->delete();
        return $clientOuterTicket;
    }

}
