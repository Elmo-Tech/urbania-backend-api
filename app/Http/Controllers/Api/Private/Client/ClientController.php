<?php

namespace App\Http\Controllers\Api\Private\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Address\CreateAddressRequest;
use App\Http\Requests\Client\Contact\CreateContactRequest;
use App\Services\Client\ClientService;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequsest;

use Illuminate\Http\Request;
class ClientController extends Controller
{
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->middleware('auth:api');
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     */
    public function allclients(Request $request)
    {
        return $this->clientService->filterClients($request);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(CreateClientRequest $clientRequest, CreateAddressRequest $AdressesRequest, CreateContactRequest $contactsRequset)
    {

        return $this->clientService->createClient($clientRequest->validated(), $AdressesRequest->validated(), $contactsRequset->validated());

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ClientService $clientService)
    {
        return $clientService->editClient($request->query('clientId'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequsest $request)
    {
        
        return $this->clientService->updateClient($request->validated());

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {   
        
        return $this->clientService->deleteClient($request->clientId);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function sinfo(Request $request)
    {   
        
        return $this->clientService->getSinfo($request);

    }
}
