<?php

namespace App\Services\Reservation;

//use App\Http\Resources\Parameter\ParameterValueResource;
use App\Models\Reservation\Reservation;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReservationService{

    public function allReservations(array $request){

        $resevations = Reservation::with('client')->get();

        return $resevations;
    }

    public function createReservation(array $reservationData){

        //$newResevationSchedule = ResevationSchedule::find($parameterData['parameterId']);
        
        $confirmationToken = $reservationData['status'] == 2? bin2hex(random_bytes(16)):null;

        $reservation = Reservation::create([
            "firstname" => $reservationData['firstname']??'',
            "lastname" => $reservationData['lastname']??'',
            "cf" => $reservationData['cf']??'',
            'p_iva' => $reservationData['pIva']??'',
            'ragione_sociale' => $reservationData['ragioneSociale']??'',
            'delegated_firstname' => $reservationData['delegatedFirstname']??'',
            'delegated_lastname' => $reservationData['delegatedLastname']??'',
            'message' => $reservationData['message']??'',
            "email" => $reservationData['email']??'',
            "phone" => $reservationData['phone']??'',
            "date" => $reservationData['date']??'',
            "duration" => $reservationData['duration']??0,
            'status' => $reservationData['status']??0,
            'parameter_id' => $reservationData['parameterId']??null,
            "client_id" => $reservationData['clientId']??null,
            'refuse_reason' => $reservationData['refuseReason']??null,
            'confirmation_token' => $confirmationToken
        ]);

        return $reservation;

    }

    public function editReservation(int $reservationId){

        $reservation = Reservation::find($reservationId);

        return $reservation;

    }

    public function updateReservation(array $reservationData): mixed{

        $reservation = Reservation::find($reservationData['reservationId']);

        if (!$reservation) {
            throw new HttpResponseException(response()->json([
                'message' => 'La sessione di prenotazione e scaduta. Effettua di nuovo la prenotazione.'
            ], 401));
        }
        
        $confirmationToken = $reservationData['status'] == 2? bin2hex(random_bytes(16)):null;

        $reservation->fill([
            "firstname" => $reservationData['firstname']??'',
            "lastname" => $reservationData['lastname']??'',
            "cf" => $reservationData['cf']??'',
            'p_iva' => $reservationData['pIva']??'',
            'ragione_sociale' => $reservationData['ragioneSociale']??'',
            'delegated_firstname' => $reservationData['delegatedFirstname']??'',
            'delegated_lastname' => $reservationData['delegatedLastname']??'',
            'message' => $reservationData['message']??'',
            "email" => $reservationData['email']??'',
            "phone" => $reservationData['phone']??'',
            "date" => $reservationData['date'],
            "duration" => $reservationData['duration']??0,
            'status' => $reservationData['status']??0,
            'parameter_id' => $reservationData['parameterId']??null,
            "client_id" => $reservationData['clientId']??null,
            'refuse_reason' => $reservationData['refuseReason']??null,
            'confirmation_token' => $confirmationToken
        ]);

        $reservation->save();

        return $reservation;

    }

    public function deleteReservation(int $reservationId){
        $reservation = Reservation::find($reservationId);
        $reservation->delete();
        return $reservation;
    }

   /* public checkResevationAvailability(array $date){
        $reservations = Reservation::where('date','=',$date)->get();*/

}
