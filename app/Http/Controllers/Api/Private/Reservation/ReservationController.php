<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Http\Resources\Reservation\AllReservationCollection;
use App\Http\Resources\Reservation\ReservationResource;
use App\Mail\ConfirmReservation;
use App\Mail\RefuseReservation;
use App\Models\Reservation\Reservation;
use App\Services\Event\EventService;
use App\Services\Reservation\ReservationService;
use App\Utils\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    protected $reservationService;
    protected $eventService;

    public function __construct(ReservationService $reservationService, EventService $eventService)
    {
        //$this->middleware('auth:api');
        $this->reservationService = $reservationService;
        $this->eventService = $eventService;
    }

    public function index(Request $request){


        $allReservations = $this->reservationService->allReservations($request->all());


        return response()->json(
            new AllReservationCollection(PaginateCollection::paginate($allReservations, $request->pageSize?$request->pageSize:10))
        );
    }

    public function create(CreateReservationRequest $createReservationRequest){

        try {

            DB::beginTransaction();


            $this->reservationService->createReservation($createReservationRequest->validated());


            DB::commit();

            return response()->json([
                'message' => 'schedule has been created !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;

        }

    }

    public function edit(Request $request)
    {
        $reservationSchedule = $this->reservationService->editReservation($request->reservationId);

        return response()->json(new ReservationResource($reservationSchedule));
    }

    public function update(UpdateReservationRequest $updateReservationRequest)
    {
        try {
            DB::beginTransaction();
            $reservation = $this->reservationService->updateReservation($updateReservationRequest->validated());

            if($reservation->status == 2){
                $this->eventService->createEvent([
                    'title' => $reservation->firstname? $reservation->firstname . ' ' . $reservation->lastname :  $reservation->ragione_sociale,
                    'description' => $reservation->message,
                    'startDate' => $reservation->date,
                    'endDate' => $reservation->date,
                    'url' => "",
                    "allDay" => 0,
                    "clientId" => $reservation->client_id,
                    "groupId" => null,
                    "ticketClientId" => $reservation->ticket_client_id
                ]);
                Mail::to($reservation->email)->send(new ConfirmReservation($reservation));
            }
            
            if($reservation->status == 0){
                Mail::to($reservation->email)->send(new RefuseReservation($reservation));
            }


            DB::commit();
            return response()->json([
                'message' => 'reservation has been updated !',
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }


    public function destroy(Request $request)
    {
        $this->reservationService->deleteReservation($request->reservationId);

        return response()->json([
            'message' => 'reservation has been deleted !',
        ]);
    }


    public function destroyUncompletedReservations(Request $request)
    {
        $thresholdTime = now()->subMinutes(15);

        // Delete reservations in bulk
        $deleteReservations = Reservation::where('status', 0)
            ->where('created_at', '<', $thresholdTime)
            ->forceDelete();

        return response()->json([
            'message' => 'reservation has been deleted !',
        ]);
    }
}
