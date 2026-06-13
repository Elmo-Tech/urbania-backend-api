<?php

namespace App\Services\Client;

use App\Http\Resources\Client\AddressResource;
use App\Models\Client;
use App\Models\ClientAddress;

class AddressService{
        
    public function createClientAddress($clientId, array $adressesData){
        foreach ($adressesData as $key => $addressData) {
            $data = (array)$addressData;
            if(!empty($addressData['address']) ||!empty( $addressData['city'])){
                $newAddress = ClientAddress::create([
                    'client_id'=> $clientId,
                    'address'=> $addressData['address']?$addressData['address']:"",
                    'city'=> $addressData['city']?$addressData['city']:"",
                    'state'=> $addressData['state']?$addressData['state']:"",
                    'postal_code'=> $addressData['postalCode']?$addressData['postalCode']:"",
                    'address_type_id'=> $addressData['addressTypeId']?$addressData['addressTypeId']:0
                ]);
            }
        }
    }

    public function createAddress(array $addressData){
        $client = Client::find($addressData['clientId']);
            
        if(!empty($addressData['address']) ||!empty( $addressData['city'])){
            $newclientAddress = ClientAddress::create([
                'client_id' => $addressData['clientId'],
                'address'=> $addressData['address']?$addressData['address']:"",
                'city'=> $addressData['city']?$addressData['city']:"",
                'state'=> $addressData['state']?$addressData['state']:"",
                'postal_code'=> $addressData['postalCode']?$addressData['postalCode']:"",
                'address_type_id'=> $addressData['addressTypeId']?$addressData['addressTypeId']:0
            ]);
        }
    }
    
    public function editAddress(int $addressId){
        $clientAddress = ClientAddress::find($addressId);

        return response()->json([
            'clientAddress' => new AddressResource($clientAddress)
        ], 500);

    }

    public function updateAddress(array $addressData){
        $clientAddress = ClientAddress::find($addressData['addressId']);
        if(!empty($addressData['address']) ||!empty( $addressData['city'])){
            $clientAddress->fill([
                'address'=> $addressData['address']?$addressData['address']:"",
                'city'=> $addressData['city']?$addressData['city']:"",
                'state'=> $addressData['state']?$addressData['state']:"",
                'postal_code'=> $addressData['postalCode']?$addressData['postalCode']:"",
                'address_type_id'=> $addressData['addressTypeId']?$addressData['addressTypeId']:0,
            ]);
        }
    
        $clientAddress->save();

    }
    public function deleteAddress(int $addressId){
        $address = ClientAddress::find($addressId);
        $address->delete();
        return response()->json([
            'message' => 'address has been deleted!'
        ], 200);

    }


}