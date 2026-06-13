<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllReservationResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'clientName' => $this->client?->company_name,
        ];

    }
}
