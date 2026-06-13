<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\AiCreateReservationRequest;
use App\Http\Requests\Reservation\AiUpdateReservationRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Services\Reservation\AiReservationService;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation\Reservation;
use Illuminate\Http\Request;


class AiCustomerReservationController extends Controller
{
    protected $reservationService;

    public function __construct(AiReservationService $reservationService)
    {
        //$this->middleware('auth:api');
        $this->reservationService = $reservationService;
    }


    public function create(AiCreateReservationRequest $createReservationRequest){

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

    public function update(AiUpdateReservationRequest $updateReservationRequest)
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
    
    
    public function control(Request $request)
    {
        try {

            DB::beginTransaction();

            $reservation = Reservation::where('id', $request->reservationId)->where('confirmation_token', $request->token)->first();
            
            if(!$reservation){
                return response()->json([
                    'message' => 'no Reservation',
                ], 422);
            }
            
            $reservation->status = $request->status;
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
