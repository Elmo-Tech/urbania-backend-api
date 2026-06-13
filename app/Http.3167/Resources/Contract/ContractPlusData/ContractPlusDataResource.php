<?php

namespace App\Http\Resources\Contract\ContractPlusData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractPlusDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contractPlusDataId' => $this->id,
            'contractId' => $this->contract_id,
            'contractParameterId' => $this->contract_parameter_id,
            'description' => $this->description?$this->description:"",
            'fatturazione' => $this->fatturazione??""
        ];
    }
}
