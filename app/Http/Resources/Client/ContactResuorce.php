<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResuorce extends JsonResource
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
            'clientId' => $this->client_id,
            'firstname' => $this->firstname?$this->firstname:"",
            'lastname' => $this->lastname?$this->lastname:"",
            'phoneNumber' => $this->phone_number?$this->phone_number:"",
            'email' => $this->email?$this->email:"",
            'roleId' => $this->role_id?$this->role_id:""
        ];
    }
}
