<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllClientResource extends JsonResource
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
            'companyName' => $this->company_name,
            'tradeRegister' => $this->trade_register,
            'peopleNumber' => $this->people_number?$this->people_number:"",
            'cf' => $this->cf?$this->cf:"",
            'contacts' => ContactResuorce::collection($this->whenLoaded('contacts')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'nameAcronym' => $this->name_acronym?? ""
        ];

    }
}
