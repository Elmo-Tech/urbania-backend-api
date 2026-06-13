<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "eventId" => $this->id,
            "title" => $this->title,
            "description" => $this->description?$this->description:"",
            "startDate" => $this->start_date?$this->start_date:"",
            "endDate" => $this->end_date?$this->end_date:"",
            "allDay" => $this->all_day,
            'clientId'=> $this->client_id,
            "groupId" => $this->groupId?$this->groupId:"",
            'ticketClientId'=> $this->ticket_client_id?$this->ticket_client_id:"",
            'url' => $this->url??""
        ];
    }
}
