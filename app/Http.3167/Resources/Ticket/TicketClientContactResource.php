<?php

namespace App\Http\Resources\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketClientContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contactId' => $this->id,
            'phoneNumber' => $this->phone_number?$this->phone_number:"",
            'email' => $this->email?$this->email:"",
        ];
    }
}
