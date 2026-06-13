<?php

namespace App\Http\Resources\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Ticket\TicketClientAddressResource;
use App\Http\Resources\Ticket\TicketClientContactResource;

class TicketClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ticketClientId' => $this->id,
            'companyName' => $this->company_name? $this->company_name: '',
            'nationalNumber' => $this->national_number?$this->national_number:"",
            'firstname' => $this->firstname?$this->firstname:"",
            'lastname' => $this->lastname?$this->lastname:"",
            'address' => TicketClientAddressResource::collection($this->whenLoaded('address')),
            'contact' => TicketClientContactResource::collection($this->whenLoaded('contact'))
        ];
    }
}
