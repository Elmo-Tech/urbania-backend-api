<?php

namespace App\Services\Reservation;

use App\Models\Event\Event;
use App\Models\Reservation\Reservation;
use App\Services\Event\EventService;

class ReservationCalendarService
{
    public function __construct(private EventService $events)
    {
    }

    // Call inside a transaction after locking the reservation, before changing its details.
    public function findEvent(Reservation $reservation): ?Event
    {
        $event = Event::withTrashed()->where('reservation_id', $reservation->id)->lockForUpdate()->first();
        if ($event) {
            return $event;
        }

        $client = $reservation->client;
        if (! $client) {
            abort_if((int) $reservation->status === 2 || $reservation->confirmation_token,
                409, 'Reservation calendar link requires manual verification.');

            return null;
        }

        // Legacy events have no foreign key. Never choose the first of several possible matches.
        $title = ($client->firstname ? $client->firstname.' '.$client->lastname : $client->company_name)
            .' | '.$this->title($reservation);
        $matches = Event::withTrashed()
            ->where('client_id', $reservation->client_id)
            ->where('title', $title)
            ->where('description', $reservation->message)
            ->where('start_date', $reservation->date)
            ->where('end_date', $reservation->date)
            ->where('ticket_client_id', $reservation->ticket_client_id)
            ->whereNull('group_id')
            ->where('url', '')
            ->where('all_day', 0)
            ->where('created_at', '>=', $reservation->created_at)
            ->lockForUpdate()->get();

        if ($matches->isEmpty()) {
            abort_if((int) $reservation->status === 2 || $reservation->confirmation_token,
                409, 'Reservation calendar link requires manual verification.');

            return null;
        }

        $otherReservation = Reservation::withTrashed()
            ->where('id', '!=', $reservation->id)
            ->where('client_id', $reservation->client_id)
            ->where('date', $reservation->date)
            ->where('message', $reservation->message)
            ->get()->contains(fn (Reservation $other) => $this->title($other) === $this->title($reservation));

        abort_if($matches->count() !== 1 || $otherReservation || $matches->first()->reservation_id !== null,
            409, 'Reservation calendar link is ambiguous and requires manual verification.');

        $event = $matches->first();
        Event::withTrashed()->whereKey($event->id)->update(['reservation_id' => $reservation->id]);
        $event->reservation_id = $reservation->id;

        return $event;
    }

    public function syncConfirmation(Reservation $reservation, ?Event $event): void
    {
        $data = [
            'title' => $this->title($reservation),
            'description' => $reservation->message,
            'startDate' => $reservation->date,
            'endDate' => $reservation->date,
            'url' => '',
            'allDay' => 0,
            'clientId' => $reservation->client_id,
            'groupId' => null,
            'ticketClientId' => $reservation->ticket_client_id,
        ];

        if ($event) {
            $this->restoreEvent($event);
            $this->events->updateEvent($data + ['eventId' => $event->id]);
        } else {
            $event = $this->events->createEvent($data);
            Event::whereKey($event->id)->update(['reservation_id' => $reservation->id]);
        }
    }

    public function deleteEvent(?Event $event): void
    {
        if ($event && ! $event->trashed()) {
            $event->delete();
        }
    }

    public function restoreEvent(Event $event): void
    {
        if ($event->trashed()) {
            // The public token endpoint has no authenticated user for the model's audit trait.
            Event::withTrashed()->whereKey($event->id)->restore();
        }
    }

    private function title(Reservation $reservation): string
    {
        return $reservation->firstname
            ? $reservation->firstname.' '.$reservation->lastname
            : (string) $reservation->ragione_sociale;
    }
}
