<?php

namespace App\Http\Resources\Contract\ContractPlusData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContractSprotelloResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contractSportelloId' => $this->id,
            'contractId' => $this->contract_id,
            'tipologiaSportello' => $this->tipologia_sportello,
            'dataIns' => $this->data_ins ? Carbon::parse($this->data_ins)->toDateTimeString() : "",
            'nOne' => $this->n_one ?? "",
            'note' => $this->note ?? "",
            'workerId' => $this->worker_id ?? ""
        ];
    }
}
