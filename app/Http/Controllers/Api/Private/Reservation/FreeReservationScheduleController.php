<?php

namespace App\Http\Controllers\Api\Private\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Client;

class FreeReservationScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->removeExpiredIncompleteReservations();

        $clientId = $request->clientId?? Client::where('company_name', $request->clientName)->first()->id;
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);

        $clientReservationSchedules = ReservationSchedule::where('client_id', $clientId)->get();

        if ($clientReservationSchedules->isEmpty()) {
            return response()->json(['error' => 'No schedule found for the client'], 404);
        }

        $schedule = $clientReservationSchedules->flatMap(fn ($row) => $row->schedule)->all();
        $result = [];

        // Iterate through each day in the date range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->toDateString();
            $freeTimes = $this->scheduledTimes($schedule, $date);

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

        $clientReservationSchedules = ReservationSchedule::where('client_id', $clientId)->get();

        if ($clientReservationSchedules->isEmpty()) {
            return response()->json(['error' => 'No schedule found for the client'], 404);
        }

        $schedule = $clientReservationSchedules->flatMap(fn ($row) => $row->schedule)->all();
        $formattedDate = $date->toDateString();
        $slots = $this->scheduledTimes($schedule, $date);

        if (empty($slots)) {
            return response()->json(['available' => false, 'message' => 'No schedule available for this day']);
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

    private function scheduledTimes(array $schedule, Carbon $date): array
    {
        $dayOfWeek = strtolower($date->format('l'));
        $formattedDate = $date->toDateString();
        $slots = [];

        // Fixed weekdays and dedicated dates both contribute availability, as before.
        foreach ($schedule as $item) {
            $matches = (($item['type'] ?? null) === 'fixed' && ($item['day'] ?? null) === $dayOfWeek)
                || (($item['type'] ?? null) === 'dedicated' && ($item['date'] ?? null) === $formattedDate);
            if (!$matches) {
                continue;
            }

            $appointmentTime = (int) ($item['appointmentTime'] ?? 0);
            $restEachTime = (int) ($item['restEachTime'] ?? 0);
            // Invalid legacy entries must not trap either endpoint in a non-advancing loop.
            if ($appointmentTime <= 0 || $restEachTime < 0) {
                continue;
            }

            foreach ($item['availableTimes'] ?? [] as $timeRange) {
                if (!is_string($timeRange) || !preg_match('/^([01]?\d|2[0-3]):[0-5]\d - ([01]?\d|2[0-3]):[0-5]\d$/', $timeRange)) {
                    continue;
                }
                [$startTime, $endTime] = explode(' - ', $timeRange);
                $currentTime = $date->copy()->setTimeFromTimeString($startTime);
                $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

                while ($currentTime->lt($endDateTime)) {
                    $slots[] = $currentTime->format('H:i');
                    $currentTime->addMinutes($appointmentTime + $restEachTime);
                }
            }
        }

        $slots = array_values(array_unique($slots));
        sort($slots);

        return $slots;
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
