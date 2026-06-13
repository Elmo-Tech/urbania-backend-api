<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationSchedule\CreateReservationScheduleRequest;
use App\Http\Requests\ReservationSchedule\UpdateReservationScheduleRequest;
use App\Http\Resources\Reservation\ReservationSchedule\AllReservationScheduleCollection;
use App\Http\Resources\Reservation\ReservationSchedule\ReservationScheduleResource;
use App\Services\Reservation\ReservationScheduleService;
use App\Utils\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReservationScheduleController extends Controller
{
    protected $reservationScheduleService;

    public function __construct(ReservationScheduleService $reservationScheduleService)
    {
        $this->middleware('auth:api');
        $this->reservationScheduleService = $reservationScheduleService;
    }

    public function index(Request $request){


        $allReservationSchedules = $this->reservationScheduleService->allReservationSchedules($request->all());


        return response()->json(
            new AllReservationScheduleCollection(PaginateCollection::paginate($allReservationSchedules, $request->pageSize?$request->pageSize:10))
        );
    }

    public function create(CreateReservationScheduleRequest $createReservationScheduleRequest){

        try {

            DB::beginTransaction();


            $this->reservationScheduleService->createReservationSchedule($createReservationScheduleRequest->validated());


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
        $reservationSchedule = $this->reservationScheduleService->editReservationSchedule($request->reservationScheduleId);

        return response()->json(new ReservationScheduleResource($reservationSchedule));
    }

    public function update(UpdateReservationScheduleRequest $updateReservationScheduleRequest)
    {
        $outerLetter = $this->reservationScheduleService->updateReservationSchedule($updateReservationScheduleRequest->validated());

        return response()->json([
            'message' => 'outerLetter has been updated !',
        ]);
    }

    public function destroy(Request $request)
    {
        $this->reservationScheduleService->deleteReservationSchedule($request->reservationScheduleId);

        return response()->json([
            'message' => 'outerLetter has been deleted !',
        ]);
    }


}
