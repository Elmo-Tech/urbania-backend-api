<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LavorazioneSecOneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "lavorazioneSecOneId" => $this->id,
            "description" => $this->description?$this->description:"",
            "years" => $this->years?explode("#", $this->years):"",
            "yearsValues" => $this->years_values?explode("#", $this->years_values):"",
            "lavorazioneSecOneParameterId" => $this->lavorazione_sec_one_parameter_id,
            "lavorazioneMainDataId" => $this->lavorazione_main_data_id
        ];
    }
}
