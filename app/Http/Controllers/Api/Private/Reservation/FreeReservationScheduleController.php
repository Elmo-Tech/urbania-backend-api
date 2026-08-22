<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reservation\ReservationSchedule\AllReservationScheduleCollection;
use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationSchedule;
use App\Services\Reservation\ReservationScheduleService;
use App\Utils\PaginateCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;

class FreeReservationScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->removeExpiredIncompleteReservations();

        $clientId = $request->clientId?? Client::where('company_name', $request->clientName)->first()->id;
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);

        // Retrieve client's reservation schedule
        $clientReservationSchedule = ReservationSchedule::where('client_id', $clientId)->first();

        if (!$clientReservationSchedule) {
            return response()->json(['error' => 'No schedule found for the client'], 404);
        }

        $schedule = $clientReservationSchedule->schedule;
        $result = [];

        // Iterate through each day in the date range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->toDateString();
            $dayOfWeek = strtolower($date->format('l')); // Example: 'monday'

            // Initialize empty array to store available times for the current date
            $freeTimes = [];

            // Process fixed schedule (e.g., Sundays)
            $fixedDaySchedule = collect($schedule)->first(function ($item) use ($dayOfWeek) {
                return $item['type'] === 'fixed' && $item['day'] === $dayOfWeek;
            });

            if ($fixedDaySchedule) {
                $availableTimes = $fixedDaySchedule['availableTimes'];
                $appointmentTime = (int) $fixedDaySchedule['appointmentTime'];
                $restEachTime = (int) $fixedDaySchedule['restEachTime'];
                $slots = [];

                // Generate time slots
                foreach ($availableTimes as $timeRange) {
                    [$startTime, $endTime] = explode(' - ', $timeRange);

                    $currentTime = $date->copy()->setTimeFromTimeString($startTime);
                    $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

                    while ($currentTime->lt($endDateTime)) {
                        $slots[] = $currentTime->format('H:i');
                        $currentTime->addMinutes($appointmentTime + $restEachTime);
                    }
                }

                // Add generated slots to freeTimes
                $freeTimes = array_merge($freeTimes, $slots);
            }

            // Process dedicated schedule (e.g., specific date like '2024-12-23')
            $dedicatedDateSchedule = collect($schedule)->first(function ($item) use ($formattedDate) {
                return $item['type'] === 'dedicated' && $item['date'] === $formattedDate;
            });



            if ($dedicatedDateSchedule) {
                $availableTimes = $dedicatedDateSchedule['availableTimes'];
                $appointmentTime = (int) $dedicatedDateSchedule['appointmentTime'];
                $restEachTime = (int) $dedicatedDateSchedule['restEachTime'];
                $slots = [];

                // Generate time slots
                foreach ($availableTimes as $timeRange) {
                    [$startTime, $endTime] = explode(' - ', $timeRange);

                    $currentTime = $date->copy()->setTimeFromTimeString($startTime);
                    $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

                    while ($currentTime->lt($endDateTime)) {
                        $slots[] = $currentTime->format('H:i');
                        $currentTime->addMinutes($appointmentTime + $restEachTime);
                    }
                }

                // Add generated slots to freeTimes
                $freeTimes = array_merge($freeTimes, $slots);
            }

            // If there are no available times for the day, skip this date
            if (empty($freeTimes)) {
                continue;
            }

            // Fetch reservations for this date
            $reservedTimes = Reservation::where('client_id', $clientId)
                ->whereDate('date', $formattedDate)
                ->pluck('date')
                ->map(function ($reservationDate) {
                    return Carbon::parse($reservationDate)->format('H:i');
                })
                ->toArray();

            // Filter out reserved times
            $freeTimes = array_values(array_map(fn($time) => ['time' => $time],
                array_filter($freeTimes, fn($slot) => !in_array($slot, $reservedTimes))
            ));

            // Add to result
            $result[] = [
                'day' => $date->format('l'), // Use the actual weekday (e.g., 'monday', 'sunday')
                'date' => $formattedDate,
                'times' => $freeTimes
            ];
        }

        return response()->json($result);
    }





    public function checkAvailability(Request $request)
    {
        $this->removeExpiredIncompleteReservations();

        $clientId = $request->clientId?? Client::where('company_name', $request->clientName)->first()->id;
        $date = Carbon::parse($request->date);
        $time = Carbon::parse($request->time);

        // Retrieve client's reservation schedule
        $clientReservationSchedule = ReservationSchedule::where('client_id', $clientId)->first();

        if (!$clientReservationSchedule) {
            return response()->json(['error' => 'No schedule found for the client'], 404);
        }

        $schedule = $clientReservationSchedule->schedule;
        $dayOfWeek = strtolower($date->format('l')); // E.g., 'sunday'
        $formattedDate = $date->toDateString();


        // Find schedule for this specific date or day
        $daySchedule = collect($schedule)->first(function ($item) use ($dayOfWeek, $formattedDate) {
            return $item['day'] === $dayOfWeek || $item['day'] === $formattedDate;
        });


        if (!$daySchedule || empty($daySchedule['availableTimes'])) {
            return response()->json(['available' => false, 'message' => 'No schedule available for this day']);
        }


        $availableTimes = $daySchedule['availableTimes'];
        $appointmentTime = (int) $daySchedule['appointmentTime'];
        $restEachTime = (int) $daySchedule['restEachTime'];
        $slots = [];

        foreach ($availableTimes as $timeRange) {
            [$startTime, $endTime] = explode(' - ', $timeRange);

            $currentTime = $date->copy()->setTimeFromTimeString($startTime);
            $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

            while ($currentTime->lt($endDateTime)) {
                $slots[] = $currentTime->format('H:i');
                $currentTime->addMinutes($appointmentTime + $restEachTime);
            }
        }

        // Fetch reservations for the specified date
        $reservations = Reservation::where('client_id', $clientId)
            ->whereDate('date', $formattedDate)
            ->get();

        $reservedTimes = $reservations->map(function ($reservation) {
            return Carbon::parse($reservation->date)->format('H:i');
        })->toArray();

        // Check if the specified time is in the slots and not reserved
        $timeString = $time->format('H:i');
        $isAvailable = in_array($timeString, $slots) && !in_array($timeString, $reservedTimes);

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'The time is available' : 'The time is not available',
        ]);
    }

    private function removeExpiredIncompleteReservations(): void
    {
        Reservation::query()
            ->where('status', 0)
            ->where('created_at', '<=', now()->subMinutes(10))
            ->where(function ($query) {
                $query->whereNull('email')->orWhere('email', '');
            })
            ->delete();
    }
}
