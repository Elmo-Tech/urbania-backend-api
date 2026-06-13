<?php

namespace App\Http\Resources\Contract\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class LavorazioneSecFourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lavorazioneSecFourId' => $this->id,
            'lavorazioneMainDataId' => $this->lavorazione_main_data_id,
            'tipologiaSportello' => $this->tipologia_sportello,
            'dataIns' => $this->data_ins ? Carbon::parse($this->data_ins)->toDateTimeString() : "",
            'nOne' => $this->n_one ?? "",
            'note' => $this->note ?? "",
            'workerId' => $this->worker_id ?? ""
        ];
    }
}
