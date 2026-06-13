<?php

namespace App\Http\Resources\OuterLetter;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OuterLetterMobileResource extends JsonResource
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
            'position' => $this->position??"",
            'name' => $this->name??"",
            'address' => $this->address??"",
            'cap' => $this->cap??"",
            'city' => $this->city??"",
            'province' => $this->province??"",
            'internalCode' => $this->internal_code??"",
            'clientId' => $this->client_id,
            'serviceId' => $this->service_id,
            'year' => $this->year??"",
            'cf' => $this->cf??"",
            'note'=> $this->note??"",
            'isOpened' => $this->is_opened,
            'receivedDate' => $this->received_date? Carbon::parse($this->received_date)->format('d/m/Y') : '',
            'openedDate' => $this->opened_date? Carbon::parse($this->opened_date)->format('d/m/Y') : '',
                        'numero' => $this->numero

        ];

    }
}
