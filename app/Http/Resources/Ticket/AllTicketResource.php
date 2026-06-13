<?php

namespace App\Http\Resources\Ticket;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        /*$nowDate = "";
        $afterNotifyDate = "";
        $remainingDays = "";
        if($this->notify_date != null){
            $nowDate = Carbon::now();
            $afterNotifyDate = Carbon::parse($this->notify_date)->addDays(60);
            $remainingDays = $afterNotifyDate->diffInDays($nowDate);
        }*/
        return [
            'ticketId' => $this->id,
            'ticketNumber' => $this->ticket_number,
            'clientName' => $this->company_name?$this->company_name:"",
            'startDate' => $this->created_at? date("d-m-Y", strtotime($this->created_at)) : "-",
            'endDate' => $this->end_date == '1970-01-01' || $this->end_date == null? "-" : date("d-m-Y", strtotime($this->end_date)),
            //'afterNotifyDate' => $this->notify_date != null? $remainingDays:"",
            'serviceName' => $this->service_name??"",
            'companyName' => $this->firm_company_name??"",
            'tipologiaIstanza' => $this->tipologiaIstanzaValue??"",
            'segnalazione' => $this->segnalazioneValue??"",
            'urgenza'=>$this->urgenzaValue??"",
            "anno" => $this->anno??"",
            'status' => $this->status,
            'workerName' => $this->firstname? $this->firstname." ".$this->lastname : "",
            'closerName' => $this->cFirstname? $this->cFirstname." ".$this->cLastname : "",
            'nominativo' => $this->tickeClientCompany?$this->tickeClientCompany:$this->nFirstname." ". $this->nLastname,
            'updatedAt' => $this->updated_at? Carbon::parse($this->updated_at)->format('d/m/Y') : ""
            //'updatedAt' => $this->updated_at? date("d-m-Y", strtotime($this->updated_at)) : "-",
        ];

    }
}
