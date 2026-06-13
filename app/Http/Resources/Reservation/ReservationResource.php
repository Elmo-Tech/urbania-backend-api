<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'reservationId' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'cf' => $this->cf,
            'pIva' => $this->p_iva,
            'ragioneSociale' => $this->ragione_sociale,
            'delegatedFirstname' => $this->delegated_firstname??'',
            'delegatedLastname' => $this->delegated_lastname??'',
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'parameterId' => $this->parameter_id,
            'clientId' => $this->client_id,
            'isAzienda' => $this->ragioneSociale?1:0
        ];

    }
}
