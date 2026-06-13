<?php

namespace App\Http\Resources\Parameter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParameterValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'parameterId' => $this->id,
            'parameterValue' => $this->parameter_value,
            'description' => $this->description??"",
            'internalCode'=>$this->internal_code??"",
            'multipleSelect'=>$this->multiple_select??"",
        ];
    }
}
