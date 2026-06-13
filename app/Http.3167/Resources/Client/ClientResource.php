<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Client\AddressResource;
use App\Http\Resources\Client\ContactResuorce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'clientId' => $this->id,
            'companyName' => $this->company_name?$this->company_name:"",
            'nationalNumber' => $this->national_number?$this->national_number:"",
            'tradeRegister' => $this->trade_register,
            'peopleNumber' => $this->people_number?$this->people_number:"",
            'cf' => $this->cf?$this->cf:"",
            'secretInfo' => $this->secret_info,
            'contacts' => ContactResuorce::collection($this->whenLoaded('contacts')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'nameAcronym' => $this->name_acronym?? ""
        ];

    }
}
