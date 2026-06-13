<?php

namespace App\Http\Resources\Select;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelectClientContractsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->id,
            'label' => $this->parameter_value . " - " . $this->contract_number
        ];
    }
}
