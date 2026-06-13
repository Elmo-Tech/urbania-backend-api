<?php

namespace App\Http\Resources\Ticket;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nowDate = "";
        $afterNotifyDate = "";
        $remainingDays = "";
        if($this->notify_date != null){
            $nowDate = Carbon::now();
            $afterNotifyDate = Carbon::parse($this->notify_date)->addDays(60);
            $remainingDays = $afterNotifyDate->diffInDays($nowDate);
        }
        return [
            'ticketId' => $this->id,
            'ticketNumber' => $this->ticket_number,
            'statusDate' => date("Y-m-d", strtotime($this->updated_at)),
            'endDate' => $this->end_date? date("Y-m-d", strtotime($this->end_date)) : "",
            'notifyDate' => $this->notify_date && $this->notify_date != "1980-01-01"? $this->notify_date:"",
            'afterNotifyDate' => $this->notify_date != null? $remainingDays:"",
            'status' => $this->status,
            'workerId' => $this->worker_id??"",
            'connectTypeId' => $this->connect_type_id > 0?$this->connect_type_id:"",
            'description' => $this->description? $this->description : '',
            'clientId' => $this->client_id,
            'ticketClientId' => $this->ticket_client_id > 0?$this->ticket_client_id:"",
            'contractId' => $this->contract_id . "##" . $this->contract_id_2,
            'serviceId' => $this->service_id,
            'esito' => $this->esito > 0?$this->esito:"",
            'note' => $this->note,
            'statusDate' => Carbon::parse($this->status_date)->format("Y-m-d"),
            'anno'=> $this->anno?explode(",", $this->anno):[],
            'tipologiaIstanza'=>$this->tipologia_istanza?$this->tipologia_istanza:"",
            'segnalazione'=>$this->segnalazione?$this->segnalazione:"",
            'urgenza'=>$this->urgenza??""


        ];
    }
}
