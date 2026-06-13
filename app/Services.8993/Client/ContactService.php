<?php

namespace App\Services\Client;

use App\Http\Resources\Client\ContactResuorce;
use App\Models\Client;
use App\Models\ClientContact;

class ContactService{
        
    public function createClientContact($clientId, array $contactsData){
        foreach ($contactsData as $key => $contactData) {

            if(!empty($contactData['phoneNumber']) || !empty($contactData['email']) || !empty($contactData['firstname']) || !empty($contactData['lastname'])){
                $newContact = ClientContact::create([
                    'client_id'=> $clientId,
                    'firstname'=> $contactData['firstname']?$contactData['firstname']:"",
                    'lastname'=> $contactData['lastname']?$contactData['lastname']:"",
                    'phone_number'=> $contactData['phoneNumber']?$contactData['phoneNumber']:"",
                    'email'=> $contactData['email']?$contactData['email']:"",
                    'role_id'=> $contactData['roleId']? $contactData['roleId'] : 0
                ]);
            }
        }
    }

    public function createContact(array $contactData){
        $client = Client::find($contactData['clientId']);
            
        if(!empty($contactData['phoneNumber']) || !empty($contactData['email']) || !empty($contactData['firstname']) || !empty($contactData['lastname'])){
            $newclientContact = ClientContact::create([
                'client_id' => $contactData['clientId'],
                'firstname'=> $contactData['firstname']?$contactData['firstname']:"",
                'lastname'=> $contactData['lastname']?$contactData['lastname']:"",
                'phone_number' => $contactData['phoneNumber']?$contactData['phoneNumber']:"",
                'email' => $contactData['email']?$contactData['email']:"",
                'role_id' => $contactData['roleId']? $contactData['roleId'] : 0
            ]);
        }

    }

    
    public function editContact(int $contactId){

        $clientContact = ClientContact::find($contactId);

        return response()->json([
            'clientContact' => new ContactResuorce($clientContact)
        ], 500);

    }

    public function updateContact(array $contactData){
        $clientContact = ClientContact::find($contactData['contactId']);
        if(!empty($contactData['phoneNumber']) || !empty($contactData['email']) || !empty($contactData['firstname']) || !empty($contactData['lastname'])){
            $clientContact->fill([
                'firstname'=> $contactData['firstname']?$contactData['firstname']:"",
                'lastname'=> $contactData['lastname']?$contactData['lastname']:"",
                'phone_number' => $contactData['phoneNumber']?$contactData['phoneNumber']:"",
                'email' => $contactData['email']?$contactData['email']:"",
                'role_id' => $contactData['roleId']? $contactData['roleId'] : 0
            ]);
            
            $clientContact->save();
        }
    }

    public function deleteContact(int $contactId){
        $contact = ClientContact::find($contactId);
        $contact->delete();
        return response()->json([
            'message' => 'contact has been deleted!'
        ], 200);

    }

}