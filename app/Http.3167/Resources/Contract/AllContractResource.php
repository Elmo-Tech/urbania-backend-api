<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllContractResource extends JsonResource
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
            'contractNumber' => $this->contract_number,
            'clientName' => $this->company_name,
            'startDate' => $this->start_date?date("d-m-Y", strtotime($this->start_date)):'-',
            'endDate' => $this->end_date?date("d-m-Y", strtotime($this->end_date)):'-',
            'services' => $this->servicesname?$this->servicesname:[],
            'companyName' => $this->cn?$this->cn:"", 
            'status' => $this->status
        ];

    }
}
