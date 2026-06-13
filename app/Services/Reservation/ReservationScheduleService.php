<?php

namespace App\Services\Reservation;

use App\Models\Reservation\ReservationSchedule;

class ReservationScheduleService{

    public function allReservationSchedules(array $request){

        $allResevationSchedules = ReservationSchedule::with('client')
            ->where('client_id', $request['clientId'])
            ->get();
        return $allResevationSchedules;
    }

    public function createReservationSchedule(array $reservationScheduleData){

        //$newResevationSchedule = ResevationSchedule::find($parameterData['parameterId']);

        $reservationSchedule = ReservationSchedule::create([
            "schedule" => $reservationScheduleData['schedule'],
            "client_id" => $reservationScheduleData['clientId'],
        ]);

        return $reservationSchedule;

    }

    public function editReservationSchedule(int $reservationScheduleId){

        $reservationSchedule = ReservationSchedule::find($reservationScheduleId);

        return $reservationSchedule;

    }

    public function updateReservationSchedule(array $reservationScheduleData): mixed{

        $reservationSchedule = ReservationSchedule::find($reservationScheduleData['reservationScheduleId']);

        $reservationSchedule->fill([
            "schedule" => $reservationScheduleData['schedule'],
            "client_id" => $reservationScheduleData['clientId'],
        ]);

        $reservationSchedule->save();

        return $reservationSchedule;

    }

    public function deleteReservationSchedule(int $reservationScheduleId){
        $reservationSchedule = ReservationSchedule::find($reservationScheduleId);
        $reservationSchedule->delete();
        return $reservationSchedule;
    }

}
