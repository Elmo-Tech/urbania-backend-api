<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AllUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     
    public function toArray(Request $request): array
    {
        return [
            'userId' =>$this->id,
            'firstname'=> $this->firstname?$this->firstname:"",
            'lastname'=> $this->lastname?$this->lastname:"",
            'username'=> $this->username?$this->username:"",
            'email'=> $this->email?$this->email:"",
            'status'=> $this->status,
        ];

    }
}
