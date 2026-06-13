<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Contract\Service\TecnicaSecOneResource;

class TecnicaMainDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tecnicaMainDataId' => $this->id,
            'contractServiceId' => $this->contract_service_id, 
            'noteSecOne'=> $this->note_sec_one?$this->note_sec_one:"",
            'tipologia'=> $this->tipologia?$this->tipologia:"",
            'dataApozione'=> $this->data_apozione?$this->data_apozione:"",
            'dataApprovazione'=> $this->data_approvazione?$this->data_approvazione:"",
            'pubblicazioneBurl'=> $this->pubblicazione_burl?$this->pubblicazione_burl:"",
            'note' => $this->note?$this->note:"",
            'tecnicaSecOne'=> TecnicaSecOneResource::collection($this->whenLoaded('tecnicaSecOne')),
            'tecnicaSecTwo'=> TecnicaSecTwoResource::collection($this->whenLoaded('tecnicaSecTwo')),
        ];
    }
}
