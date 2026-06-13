<?php

namespace App\Http\Resources\OuterLetter;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllOuterLetterMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'outerLetterId' => $this->id,
            'letterId' => $this->letter_id??"",
            'name' => $this->name??"",
            'address' => $this->address??"",
            'cap' => $this->cap??"",
            'city' => $this->city??"",
            'province' => $this->province??"",
            'internalCode' => $this->internal_code??"",
            'clientName' => $this->clients->getName(),
            'serviceName' => $this->services->parameter_value??"",
            'year' => $this->year??"",
            'cf' => $this->cf??"",
            'note'=> $this->note??"",
            'isOpened' => $this->is_opened,
            'receivedDate' => $this->received_date? Carbon::parse($this->received_date)->format('d/m/Y') : '',
            'numero' => $this->numero
        ];

    }
}
