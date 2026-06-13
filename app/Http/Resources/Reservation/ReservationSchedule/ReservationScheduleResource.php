<?php

namespace App\Http\Resources\Reservation\ReservationSchedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ReservationScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'reservationScheduleId' => $this->id,
            'schedule' => $this->schedule,
            'clientId' => $this->client_id
        ];

    }
}
