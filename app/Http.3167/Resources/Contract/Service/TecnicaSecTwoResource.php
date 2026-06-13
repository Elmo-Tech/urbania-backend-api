<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TecnicaSecTwoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "tecnicaSecTwoId" => $this->id,
            "tecnicaMainDataId" => $this->tecnica_main_data_id,
            'tipologia'=> $this->tipologia?$this->tipologia:"",
            'dataApozione'=> $this->data_apozione?$this->data_apozione:"",
            'dataApprovazione'=> $this->data_approvazione?$this->data_approvazione:"",
            'pubblicazioneBurl'=> $this->pubblicazione_burl?$this->pubblicazione_burl:"",
            'note' => $this->note?$this->note:"",
        ];
    }
}
