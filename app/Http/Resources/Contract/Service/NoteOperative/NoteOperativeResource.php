<?php

namespace App\Http\Resources\Contract\Service\NoteOperative;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteOperativeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'noteOperativeId' => $this->id,
            'date' => $this->date??"",
            'description' => $this->description??"",
        ];
    }
}
