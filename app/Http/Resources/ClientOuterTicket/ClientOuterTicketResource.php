<?php

namespace App\Http\Resources\ClientOuterTicket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ClientOuterTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'clientOuterTicketId' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'cf' => $this->cf,
            'pIva' => $this->p_iva,
            'ragioneSociale' => $this->ragione_sociale,
            'delegatedFirstname' => $this->delegated_firstname??'',
            'delegatedLastname' => $this->delegated_lastname??'',
            'delegatedPhone' => $this->delegated_phone??'',
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'message' => $this->message,
            'anno' => $this->anno,
            'status' => $this->status,
            'serviceId' => $this->service_id,
            'istanzaParameterId' => $this->istanza_parameter_id,
            'delegatedRoleId' => $this->delegated_role_id,
            'clientId' => $this->client_id,
            'acceptStatus' => $this->accept_status,
            'urgenza' => $this->urgenza,
            'notifyDate' => $this->notify_date,
            'contractId' => $this->contract_id."##".$this->contract_two_id,
            'ticketClientId' => $this->ticket_client_id,
            'endDate' => $this->end_date,
            'connectTypeId' => $this->connect_type_id,
            'esito' => $this->esito,
            'note' => $this->note,
            'segnalazione' => $this->segnalazione,
            'workerId' => $this->worker_id



        ];

    }
}
