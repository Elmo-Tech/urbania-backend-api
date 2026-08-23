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

    private function findTicketClientByIdentity(?string $clientIdentity): ?TicketClient
    {
        $clientIdentity = is_string($clientIdentity) ? trim($clientIdentity) : $clientIdentity;

        if (empty($clientIdentity)) {
            return null;
        }

        return TicketClient::where('national_number', $clientIdentity)->first();
    }

    public function allClientOuterTickets(array $request){

        $clientOuterTickets = ClientOuterTicket::with('client')->get();

        return $clientOuterTickets;
    }

    public function createClientOuterTicket(array $clientOuterTicketData){

        //$newResevationSchedule = ResevationSchedule::find($parameterData['parameterId']);
        $cf = $clientOuterTicketData['cf']??$clientOuterTicketData['pIva']??null;
        $ticketClient = $this->findTicketClientByIdentity($cf);

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

        $clientOuterTicket = ClientOuterTicket::findOrFail($clientOuterTicketData['clientOuterTicketId']);
        $currentStatus = (string) $clientOuterTicket->status;
        $newStatus = (string) ($clientOuterTicketData['status'] ?? 0);
        $message = $clientOuterTicketData['message'] ?? $clientOuterTicketData['description'] ?? '';
        $istanzaParameterId = $clientOuterTicketData['istanzaParameterId'] ?? $clientOuterTicketData['tipologiaIstanza'] ?? null;

        $clientOuterTicket->firstname = $clientOuterTicketData['firstname'] ?? '';
        $clientOuterTicket->lastname = $clientOuterTicketData['lastname'] ?? '';
        $clientOuterTicket->cf = $clientOuterTicketData['cf'] ?? '';
        $clientOuterTicket->p_iva = $clientOuterTicketData['pIva'] ?? '';
        $clientOuterTicket->ragione_sociale = $clientOuterTicketData['ragioneSociale'] ?? '';
        $clientOuterTicket->delegated_firstname = $clientOuterTicketData['delegatedFirstname'] ?? '';
        $clientOuterTicket->delegated_lastname = $clientOuterTicketData['delegatedLastname'] ?? '';
        $clientOuterTicket->delegated_phone = $clientOuterTicketData['delegatedPhone'] ?? '';
        $clientOuterTicket->message = $message;
        $clientOuterTicket->email = $clientOuterTicketData['email'] ?? '';
        $clientOuterTicket->address = $clientOuterTicketData['address'] ?? '';
        $clientOuterTicket->city = $clientOuterTicketData['city'] ?? null;
        $clientOuterTicket->state = $clientOuterTicketData['state'] ?? null;
        $clientOuterTicket->phone = $clientOuterTicketData['phone'] ?? '';
        $clientOuterTicket->status = $newStatus;
        $clientOuterTicket->anno = $clientOuterTicketData['anno'] ?? '';
        $clientOuterTicket->service_id = $clientOuterTicketData['serviceId'] ?? null;
        $clientOuterTicket->istanza_parameter_id = $istanzaParameterId;
        $clientOuterTicket->delegated_role_id = $clientOuterTicketData['delegatedRoleId'] ?? null;
        $clientOuterTicket->client_id = $clientOuterTicketData['clientId'] ?? null;
        $clientOuterTicket->status_date = $newStatus === $currentStatus ? $clientOuterTicket->status_date : Carbon::now();


        $contract = array_pad(explode("##", (string) ($clientOuterTicketData['contractId'] ?? '')), 2, null);

        $parameterValue = null;

        if(!empty($clientOuterTicketData['urgenza'])){
            $parameterValue = DB::table('parameter_values')->select('description')->where('id', $clientOuterTicketData['urgenza'])->first();
        }

        if (($currentStatus !== "2" && $newStatus === "2") || ($currentStatus !== "3" && $newStatus === "3")) {
            $clientOuterTicket->closer_id = Auth::id();
        }


        if($newStatus === "1" && $clientOuterTicket->email_token == null){
            $clientOuterTicket->email_token = str::random(40);
            $afterNotifyDate = null;
            if(!empty($clientOuterTicketData['notifyDate'])){
                $date = Carbon::parse($clientOuterTicketData['notifyDate']);
                $afterNotifyDate = $date->addDays(60);
            }

            $clientOuterTicket->notify_date = $afterNotifyDate;
        }

        $ticketClientId = $clientOuterTicket->ticket_client_id;
        $requestTicketClientId = $clientOuterTicketData['ticketClientId'] ?? null;
        $clientIdentity = $clientOuterTicketData['cf'] ?? $clientOuterTicketData['pIva'] ?? null;

        if($requestTicketClientId === "0" || $requestTicketClientId === 0){

            $ticketClientId = null;

        } elseif($requestTicketClientId === "" || $requestTicketClientId === null) {

            if($ticketClientId === null){
                $ticketClient = $this->findTicketClientByIdentity($clientIdentity);

                if(!$ticketClient){
                    $ticketClient = TicketClient::create([
                        'firstname'=> $clientOuterTicketData['firstname'],
                        'lastname'=> $clientOuterTicketData['lastname'],
                        'company_name'=> $clientOuterTicketData['ragioneSociale'],
                        'national_number'=> $clientIdentity,
                    ]);
                }

                $ticketClientId = $ticketClient->id;
            } else {
                $ticketClient = TicketClient::find($ticketClientId);
            }

            if($ticketClient){
                $ticketClient->firstname = $clientOuterTicketData['firstname'];
                $ticketClient->lastname = $clientOuterTicketData['lastname'];
                $ticketClient->company_name = $clientOuterTicketData['ragioneSociale'];
                $ticketClient->national_number = $clientIdentity;
                $ticketClient->save();

                $ticketClientContact = TicketClientContact::firstOrNew([
                    'ticket_client_id' => $ticketClient->id
                ]);
                $ticketClientContact->phone_number = $clientOuterTicketData['phone'];
                $ticketClientContact->email = $clientOuterTicketData['email'];
                $ticketClientContact->save();

                $ticketClientAddress = TicketClientAddress::firstOrNew([
                    'ticket_client_id' => $ticketClient->id
                ]);
                $ticketClientAddress->address = $clientOuterTicketData['address'];
                $ticketClientAddress->city = $clientOuterTicketData['city'] ?? null;
                $ticketClientAddress->state = $clientOuterTicketData['state'] ?? null;
                $ticketClientAddress->postal_code = $clientOuterTicketData['postalCode'] ?? null;
                $ticketClientAddress->save();
            }

        } elseif($requestTicketClientId >= 1){

            $ticketClientId = $requestTicketClientId;

            $ticketClient = TicketClient::find($requestTicketClientId);

            if($ticketClient){
                $ticketClient->firstname = $clientOuterTicketData['firstname'];
                $ticketClient->lastname = $clientOuterTicketData['lastname'];
                $ticketClient->company_name = $clientOuterTicketData['ragioneSociale'];
                $ticketClient->national_number = $clientIdentity;
                $ticketClient->save();

                $ticketClientContact = TicketClientContact::firstOrNew([
                    'ticket_client_id' => $requestTicketClientId
                ]);
                $ticketClientContact->phone_number = $clientOuterTicketData['phone'];
                $ticketClientContact->email = $clientOuterTicketData['email'];
                $ticketClientContact->save();

                $ticketClientAddress = TicketClientAddress::firstOrNew([
                    'ticket_client_id' => $requestTicketClientId
                ]);
                $ticketClientAddress->address = $clientOuterTicketData['address'];
                $ticketClientAddress->city = $clientOuterTicketData['city'] ?? null;
                $ticketClientAddress->state = $clientOuterTicketData['state'] ?? null;
                $ticketClientAddress->postal_code = $clientOuterTicketData['postalCode'] ?? null;
                $ticketClientAddress->save();
            }

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
        $clientOuterTicket->urgenza = $parameterValue->description ?? $clientOuterTicket->urgenza ?? "0";
        $clientOuterTicket->accept_status = $clientOuterTicketData['acceptStatus'] ?? 0;
        $clientOuterTicket->worker_id = $clientOuterTicketData['workerId'] === "" ? null : ($clientOuterTicketData['workerId'] ?? null);

        $clientOuterTicket->save();

        return $clientOuterTicket;

    }

    public function deleteClientOuterTicket(int $clientOuterTicketId){
        $clientOuterTicket = ClientOuterTicket::find($clientOuterTicketId);
        $clientOuterTicket->delete();
        return $clientOuterTicket;
    }

}
