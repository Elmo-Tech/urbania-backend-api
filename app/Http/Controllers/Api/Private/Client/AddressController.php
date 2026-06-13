<?php

namespace App\Http\Controllers\Api\Private\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\CreateSingleAddressRequest;
use App\Http\Requests\Client\Address\UpdateSingleAddressRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AddressResource;
use App\Models\ClientAddress;
use App\Models\Client;
use App\Services\Client\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->middleware('auth:api');
        $this->addressService = $addressService;
    }

    public function create(CreateSingleAddressRequest $addressRequest){

        return $this->addressService->createAddress($addressRequest->validated());

    }


    public function edit(Request $request, AddressService $addressService){

        return $addressService->editAddress($request->addressId);

    }

    public function update(UpdateSingleAddressRequest $addressRequest, AddressService $addressService){

        return $addressService->updateAddress($addressRequest->validated());
    }


    /**
    * Remove the specified resource from storage.
    */
    public function delete(Request $request)
    {   
        return $this->addressService->deleteAddress($request->addressId);
    }
    
}
