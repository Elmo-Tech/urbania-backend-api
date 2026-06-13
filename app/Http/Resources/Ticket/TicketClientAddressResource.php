<?php

namespace App\Http\Resources\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketClientAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'addressId' => $this->id,
            'address' => $this->address?$this->address:"",
            'city' => $this->city?$this->city:"",
            'state' => $this->state?$this->state:"",
            'postalCode' => $this->postal_code?$this->postal_code:""
        ];
    }
}
