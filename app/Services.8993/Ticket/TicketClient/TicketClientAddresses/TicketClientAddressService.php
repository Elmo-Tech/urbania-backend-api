<?php

namespace App\Services\Ticket\TicketClient\TicketClientAddresses;

use App\Models\TicketClientAddress;
use Illuminate\Support\Facades\Auth;

class TicketClientAddressService{
        
   /* public function createTicketClientContact($ticketClientId, array $contactsData){

        $data = $contactsData['contacts'];
        
        foreach ($data as $key => $contactData) {
            $newContact = TicketClientAddress::create([
                'ticket_client_id'=> $ticketClientId,
                'address'=> $contactData['address'],
                'state'=> $contactData['state'],
                'postal_code'=> $contactData['postalCode'],
                'created_by'=> Auth::id(),
                'updated_by'=> Auth::id()
            ]);
        }
    }*/

    public function createTicketClientSingleAddress($ticketClientId, array $addressData){

            $newAddress = TicketClientAddress::create([
                'ticket_client_id'=> $ticketClientId,
                'address'=> $addressData['address'],
                'city'=> $addressData['city'],
                'state'=> $addressData['state'],
                'postal_code'=> $addressData['postalCode'],
            ]);
    }

    public function updateTicketClientSingleAddress(array $addressData){

        $address = TicketClientAddress::find($addressData['addressId']);
        $address->fill([
            'address'=> $addressData['address'],
            'city'=> $addressData['city'],
            'state'=> $addressData['state'],
            'postal_code'=> $addressData['postalCode'],
        ]);

        $address->save();
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