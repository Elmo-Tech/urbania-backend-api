<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Contract\ContractSingleServiceResource;

class ContractWithServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'contractId' => $this->id,
            'clientId' => $this->client_id,
            'contractNumber' => $this->contract_number,
            'companyId' => $this->company_id,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'status' => $this->status,
            'cig' => $this->cig,
            'cup' => $this->cup,
            'note' => $this->note??"",
            'impostaId' => $this->imposta_id,
            'signDate' => $this->sign_date?$this->sign_date:"",
            'services'=> ContractSingleServiceResource::collection($this->whenLoaded('contractService'))??[]
        ];
        //return parent::toArray($request);
    }
}
