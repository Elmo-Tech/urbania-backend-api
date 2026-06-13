<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TecnicaSecOneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "tecnicaSecOneId" => $this->id,
            "description" => $this->description?$this->description:"",
            "tecnicaSecOneParameterId" => $this->tecnica_sec_one_parameter_id,
            "tecnicaMainDataId" => $this->tecnica_main_data_id
        ];
    }
}
