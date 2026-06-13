<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LavorazioneSecThreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "lavorazioneSecThreeId" => $this->id,
            'imposta'=> $this->imposta?$this->imposta:"",
            'note' => $this->note?$this->note:"",
            'nAvvisi' => $this->n_avvisi?$this->n_avvisi:"",
            'importa' => $this->importa?$this->importa:"",
            'annoEnnissone' => $this->anno_ennissone?$this->anno_ennissone:"",
            'annoAccertamento' => $this->anno_accertamento?$this->anno_accertamento:"",
            "lavorazioneMainDataId" => $this->lavorazione_main_data_id
        ];
    }
}
