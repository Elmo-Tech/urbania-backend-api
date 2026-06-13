<?php

namespace App\Http\Resources\Select;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelectServiceContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->value . "##" . $this->contract_id,
            'label' => $this->label . ' - ' .$this->company
        ];
    }
}
