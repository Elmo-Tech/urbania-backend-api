<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Contract\Service\LavorazioneSecOneResource;
use App\Http\Resources\Contract\Service\LavorazioneSecTwoResource;

class LavorazioneMainDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lavorazioneMainDataId' => $this->id,
            'contractServiceId' => $this->contract_service_id, 
            'noteSecOne'=> $this->note_sec_one?$this->note_sec_one:"",
            'noteSecTwo'=> $this->note_sec_two?$this->note_sec_two:"",
            'noteSecThree'=> $this->note_sec_three?$this->note_sec_three:"",
            'noteSecFour'=> $this->note_sec_four?$this->note_sec_four:"",
            'imposta'=> $this->imposta?$this->imposta:"",
            'note' => $this->note?$this->note:"",
            'nAvvisi' => $this->n_avvisi?$this->n_avvisi:"",
            'importa' => $this->importa?$this->importa:"",
            'annoEnnissone' => $this->anno_ennissone?$this->anno_ennissone:"",
            'annoAccertamento' => $this->anno_accertamento?$this->anno_accertamento:"",
            'lavorazioneSecOne'=> LavorazioneSecOneResource::collection($this->whenLoaded('lavorazioneSecOne')),
            'lavorazioneSecTwo'=> LavorazioneSecTwoResource::collection($this->whenLoaded('lavorazioneSecTwo')),
            'lavorazioneSecThree'=> LavorazioneSecThreeResource::collection($this->whenLoaded('lavorazioneSecThree')),
            'lavorazioneSecFour'=> LavorazioneSecFourResource::collection($this->whenLoaded('contractSportello'))

        ];
    }
}
