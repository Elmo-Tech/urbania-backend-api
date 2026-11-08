<?php

namespace App\Services\Client;

use App\Http\Resources\Client\AllClientCollection;
use App\Http\Resources\Client\ClientResource;
use App\Services\Client\AddressService;
use App\Services\Client\ContactService;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ClientService{

    protected $addressService;
    protected $contactService;
    private $clients;
    private $exportService;

    public function __construct(AddressService $addressService, ContactService $contactService, Client $clients, ClientExportService $exportService)
    {
        $this->addressService = $addressService;
        $this->contactService = $contactService;
        $this->clients = $clients;
        $this->exportService = $exportService;
    }

    public function filterClients(Object $request){
        $allClients = $this->filteredClientsQuery($request);

        return response()->json([
            'clientPage' => new AllClientCollection($allClients->paginate($request->pageSize))
        ], 200);

    }

    public function exportClients(Object $request): StreamedResponse
    {
        return $this->exportService->download(
            $this->filteredClientsQuery($request),
            $request->exportType
        );
    }

    private function filteredClientsQuery(Object $request): Builder
    {
        $query = $this->clients->newQuery();
        $query->where('id', $request->clientId ? '=' : '!=', $request->clientId ?: 0);

        if (isset($request->tradeRegister) && $request->tradeRegister !== '') {
            $query->where('trade_register', 'LIKE', "%{$request->tradeRegister}%");
        }

        if (isset($request->cf) && $request->cf !== '') {
            $query->where('cf', 'LIKE', "%{$request->cf}%");
        }

        if (isset($request->peopleNumber)) {
            [$operator, $number] = strlen($request->peopleNumber) < 2
                ? ['>=', 0]
                : explode('|', $request->peopleNumber, 2);
            $query->where('people_number', $operator, (int) $number);
        }

        return $query->whereNull('deleted_at');
    }

    public function createClient(array $userData, array $clientAddresses, array $clientContacts){
        
       $client = Client::create([
            'company_name'=> $userData['companyName'],
            'trade_register'=> $userData['tradeRegister'],
            'cf'=> $userData['cf'],
            'people_number'=> $userData['peopleNumber']?$userData['peopleNumber']:"0",
            'secret_info'=> Crypt::encryptString($userData['secretInfo']),
            'name_acronym' => $userData['nameAcronym'],
            'urbania_client' => $userData['urbaniaClient']
            
        ]);
        

        if(isset($clientAddresses['addresses'])){
            $getClientAddress = $clientAddresses['addresses'];
            $this->addressService->createClientAddress($client->id,  $getClientAddress);

        }

        if(isset($clientContacts['contacts'])){
            $getclientContacts = $clientContacts['contacts'];
            $this->contactService->createClientContact($client->id,  $getclientContacts);
    
        }

        return response()->json([
            'message' => 'client has been created!'
        ], 200);

    }

    public function editClient(int $clientId){
        $client = Client::where('id', '=', $clientId)->with('addresses')->with('contacts')->get();
        return response()->json(ClientResource::collection($client)[0], 200);
    }

    public function updateClient(array $clientData){

        $secretInfo = strlen($clientData['newSecretInfo']) > 0 ? Crypt::encryptString($clientData['newSecretInfo']) : $clientData['oldSecretInfo'];

        $client = Client::find($clientData['clientId']);

        $client->fill([
            'company_name'=> $clientData['companyName'],
            'trade_register'=> $clientData['tradeRegister'],
            'cf'=> $clientData['cf'],
            'people_number'=> $clientData['peopleNumber']?$clientData['peopleNumber']:"0",
            'secret_info'=> $secretInfo,
            'name_acronym' => $clientData['nameAcronym'],
            'urbania_client' => $clientData['urbaniaClient']

        ]);
        
        $client->save();

        return response()->json([
            'message' => 'client has been updated!'
        ], 200);

    }

    public function deleteClient(int $clientId){
        $client = Client::find($clientId);
        $client->delete();
        $client->addresses()->delete();
        $client->contacts()->delete();
        return response()->json([
            'message' => 'client has been deleted!'
        ], 200);

    }

    public function getSInfo(Object $request){

        $userToken = Auth::attempt(['username' => $request->username, 'password' => $request->password]);
        
        if(!$userToken){
            return response()->json([
                'message' => 'Username Or Password is Wrong',
            ], 401);
        }
        
        $client = Client::find($request->clientId);

        $text = Crypt::decryptString($client->secret_info);


        return response()->json([
            'message' => $text
        ], 200);

    }

   
}
