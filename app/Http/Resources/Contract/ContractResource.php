<?php

namespace App\Http\Resources\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'status' => $this->status,
            'equ' => $this->equ,
            'note' => $this->note??""
        ];
    }
}
