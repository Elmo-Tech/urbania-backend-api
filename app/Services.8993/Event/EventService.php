<?php

namespace App\Services\Event;

use App\Http\Resources\Event\EventResource;

//use App\Http\Resources\Parameter\ParameterValueResource;
use App\Models\Event\Event;

class EventService{

    public function getAllEvents(object $request){
        
        $clientId = isset($request->clientId)? ['=', $request->clientId]:['!=', '0'];
        $startDate = $request->startDate? ['>=', $request->startDate]:['!=', '0'];
        $endDate = $request->endDate? ['<=', $request->endDate]:['!=', '0'];

        $allEvents = Event::select('events.*', 'clients.company_name', 'ticket_clients.firstname', 'ticket_clients.lastname')
        ->leftJoin('clients', 'clients.id', '=', 'events.client_id')
        ->leftJoin('ticket_clients', 'ticket_clients.id', '=', 'events.ticket_client_id')
        ->when(isset($request->clientId), function ($query) use ($clientId) {
            return $query->where('client_id', $clientId[0], $clientId[1]);
        })
        ->when(isset($request->startDate) && $startDate != null, function ($query) use ($startDate, $endDate) {
            return $query->whereDate('start_date', $startDate[0], $startDate[1])
                        ->whereDate('start_date', $endDate[0], $endDate[1]);
        })
        ->whereNull('events.deleted_at')
        ->get();

        return $allEvents;
    }
        
    public function createEvent(array $eventData){
        
        //$newEvent = Event::find($parameterData['parameterId']);
            
        $newEvent = Event::create([
            "title" => $eventData['title'],
            "description" => $eventData['description'],
            "start_date" => $eventData['startDate'],
            "end_date" => $eventData['endDate'],
            "url" => $eventData['url'],
            "all_day" => $eventData['allDay'],
            "client_id" => $eventData['clientId'],
            "group_id" => $eventData['groupId'],
            "client_id" => $eventData['clientId'],
            "ticket_client_id" => $eventData['ticketClientId']
        ]);

        return $newEvent;

    }
    
    public function editEvent(int $eventId){

        $event = Event::find($eventId);

        return $event;

    }

    public function updateEvent(array $eventData){
        
        $event = Event::find($eventData['eventId']);
        
        $event->fill([
            "title" => $eventData['title'],
            "description" => $eventData['description'],
            "start_date" => $eventData['startDate'],
            "end_date" => $eventData['endDate'],
            "url" => $eventData['url'],
            "all_day" => $eventData['allDay'],
            "client_id" => $eventData['clientId'],
            "group_id" => $eventData['groupId'],
            "client_id" => $eventData['clientId'],
            "ticket_client_id" => $eventData['ticketClientId']
        ]);

        $event->save();

        return $event;

    }

    public function deleteEvent(int $eventId){
        $event = Event::find($eventId);
        $event->delete();
        return $event;

    }

}