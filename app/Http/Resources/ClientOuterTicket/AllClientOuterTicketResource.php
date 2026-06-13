<?php

namespace App\Http\Resources\ClientOuterTicket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllClientOuterTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'outerTicketId' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date,
            'status' => $this->status,
            'clientName' => $this->client?->company_name,
        ];

    }
}
