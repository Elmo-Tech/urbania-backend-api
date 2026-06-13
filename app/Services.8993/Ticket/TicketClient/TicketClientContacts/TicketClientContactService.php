<?php

namespace App\Services\Ticket\TicketClient\TicketClientContacts;

use App\Models\TicketClientContact;
use Illuminate\Support\Facades\Auth;

class TicketClientContactService{
        
    public function createTicketClientContact($ticketClientId, array $contactsData){

        $data = $contactsData['contacts'];
        
        foreach ($data as $key => $contactData) {
            $newContact = TicketClientContact::create([
                'ticket_client_id'=> $ticketClientId,
                'phone_number'=> $contactData['phoneNumber'],
                'email'=> $contactData['email'],
            ]);
        }
    }

    public function createTicketClientSingleContact($ticketClientId, array $contactData){

            $newContact = TicketClientContact::create([
                'ticket_client_id'=> $ticketClientId,
                'phone_number'=> $contactData['phoneNumber'],
                'email'=> $contactData['email'],
            ]);
    }

    public function updateTicketClientSingleContact(array $contactData){

        $contact = TicketClientContact::find($contactData['contactId']);
        
        $contact->fill([
            'phone_number'=> $contactData['phoneNumber'],
            'email'=> $contactData['email'],
        ]);

        $contact->save();

}

/*    public function editContact(int $contactId){
        $clientContact = ClientContact::find($contactId);

        return response()->json([
            'clientContact' => ContactResuorce::collection($clientContact)
        ], 500);

    }

    public function updateContact(int $contactData){
        $clientContact = ClientContact::find($contactData['contactId']);
        $clientContact->fill([
            'firstname' => $contactData['firstname'],
            'lastname' => $contactData['lastname'],
            'phone_number' => $contactData['phoneNumber'],
            'email' => $contactData['email'],
            'role_id' => $contactData['roleId']
        ]);
        

    }

    public function deleteContact(int $contactId){
        $contact = ClientContact::find($contactId);
        $contact->delete();
        return response()->json([
            'message' => 'contact has been deleted!'
        ], 200);

    }*/

}