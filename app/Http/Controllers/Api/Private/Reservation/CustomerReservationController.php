<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Services\Reservation\ReservationService;
use App\Services\Reservation\ReservationCalendarService;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation\Reservation;
use Illuminate\Http\Request;


class CustomerReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        //$this->middleware('auth:api');
        $this->reservationService = $reservationService;
    }


    public function create(CreateReservationRequest $createReservationRequest){

        try {

            DB::beginTransaction();

            $reservation = $this->reservationService->createReservation($createReservationRequest->validated());

            DB::commit();

            return response()->json([
                'message' => 'schedule has been created !',
                'data' => new ReservationResource($reservation)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function update(UpdateReservationRequest $updateReservationRequest)
    {
        try {

            DB::beginTransaction();

            $reservation = $this->reservationService->updateReservation($updateReservationRequest->validated());

            DB::commit();

            return response()->json([
                'message' => 'schedule has been updated !',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    
    public function control(Request $request, ReservationCalendarService $calendarService)
    {
        $data = $request->validate([
            'reservationId' => 'required|integer',
            'token' => 'required|string',
            'status' => 'required|integer|in:0,2',
        ]);
        try {

            DB::beginTransaction();

            $reservation = Reservation::where('id', $data['reservationId'])
                ->where('confirmation_token', $data['token'])->lockForUpdate()->first();
            
            if(!$reservation){
                DB::rollBack();
                return response()->json([
                    'message' => 'no Reservation',
                ], 422);
            }
            
            $event = $calendarService->findEvent($reservation);
            if ((int) $data['status'] === 0) {
                $calendarService->deleteEvent($event);
            } else {
                abort_unless($event, 409, 'Reservation calendar link requires manual verification.');
                $calendarService->restoreEvent($event);
            }

            $reservation->status = $data['status'];
            $reservation->save();

            DB::commit();

            return response()->json([
                'message' => 'schedule has been updated !',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
