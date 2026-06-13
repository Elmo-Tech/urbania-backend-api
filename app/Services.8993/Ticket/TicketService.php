<?php

namespace App\Services\Ticket;

use App\Http\Resources\Ticket\AllTicketCollection;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Ticket;
use App\Services\Ticket\TicketClient\TicketClientService;
use App\Utils\PaginateCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class TicketService{

    protected $ticketClientService;

    public function __construct(TicketClientService $ticketClientService)
    {
        $this->ticketClientService = $ticketClientService;
    }

    public function alltickets(Object $request)
    {
        $clientId = $request->clientId? ['=', $request->clientId]:['!=', '0'];
        $companyId = $request->companyId? ['=', $request->companyId]:['!=', '0'];

        $ticketClientId = $request->ticketClientId? ['=', $request->ticketClientId]:['!=', null];
        $serviceId = $request->serviceId? ['=', $request->serviceId]:['!=', '0'];
        $userId = $request->userId? ['=', $request->userId]:['!=', '0'];
        $esito = $request->esito? ['=', $request->esito]:['!=', '0'];
        $nominativo = $request->nominativo ? $request->nominativo : null;

        //$startDate = $request->startDate? explode("|", $request->startDate) : ["!=", "1980-01-01"];
        //$endDate = $request->endDate? explode("|", $request->endDate) : ["!=", ""];
        $ticketNumber = "%" . $request->ticketNumber . "%";
        $ticketNumber = $request->ticketNumber? ['LIKE', $ticketNumber] : ["!=", null];
        $status = $request->status !== null? ['=', $request->status] : ["!=", null];
        $remain = $request->remain? 1 : 0;
        $tipologiaIstanza = $request->tipologiaIstanza? ['=', $request->tipologiaIstanza]:['!=', '0'];
        $segnalazione = $request->segnalazione? ['=', $request->segnalazione]:['!=', '0'];
        $anno = $request->anno? explode(",", $request->anno):null;
        $urgenza = $request->urgenza? ['=', $request->urgenza]:['!=', '0'];
        
        $codiceFiscale = $request->codiceFiscale? ['=', $request->codiceFiscale]:['!=', '0'];
        $supendedTicketsMoreThanTwoWeeks = $request->suspendedTickets == 1? $request->suspendedTickets : null;
    

       $allTickets = DB::table('tickets')
            ->select('tickets.id', 'tickets.ticket_number', 'tickets.created_at', 'tickets.updated_at','tickets.end_date', 'tickets.status', 'tickets.anno', 'tickets.updated_by', 'clients.company_name', 'ticket_clients.firstname as nFirstname', 'ticket_clients.lastname as nLastname', 'ticket_clients.company_name as tickeClientCompany', 'services.parameter_value as service_name', 'companies.parameter_value as firm_company_name', 'workers.lastname', 'workers.firstname', 'workers.username','closers.lastname as cLastname', 'closers.firstname as cFirstname', 'closers.username as cUsername',/*'updaters.lastname as uLastname', 'updaters.firstname as uFirstname', 'updaters.username as uUsername',*/ 'tipologiaIstanza.parameter_value as tipologiaIstanzaValue' , 'segnalazione.parameter_value as segnalazioneValue', DB::raw('(SELECT parameter_values.parameter_value FROM parameter_values WHERE parameter_values.description = tickets.urgenza AND parameter_values.deleted_at is null AND parameter_values.parameter_id = 17 LIMIT 1) AS urgenzaValue'))
            ->leftJoin('parameter_values as services', 'services.id', '=', 'tickets.service_id')
            ->leftJoin('clients', 'clients.id', '=', 'tickets.client_id')
            ->leftJoin('ticket_clients', 'ticket_clients.id', '=', 'tickets.ticket_client_id')
            ->leftJoin('users as workers', 'workers.id', '=', 'tickets.worker_id')
            ->leftJoin('users as closers', 'closers.id', '=', 'tickets.closer_id')
            //->leftJoin('users as updaters', 'updaters.id', '=', 'tickets.updated_by')
            ->leftJoin('contracts', 'contracts.id', '=', 'tickets.contract_id_2')
            ->leftJoin('parameter_values as companies', 'companies.id', '=', 'contracts.company_id')
            ->leftJoin('parameter_values as tipologiaIstanza', 'tipologiaIstanza.id', '=', 'tickets.tipologia_istanza')
            ->leftJoin('parameter_values as segnalazione', 'segnalazione.id', '=', 'tickets.segnalazione')

        ->when(isset($request->clientId), function ($query) use ($clientId) {
            return $query->where('tickets.client_id', $clientId[0], $clientId[1]);
        })
        ->when(isset($request->codiceFiscale), function ($query) use ($codiceFiscale) {
            return $query->where('tickets.ticket_client_id', $codiceFiscale[0], $codiceFiscale[1]);
        })
        ->when(isset($request->serviceId), function ($query) use ($serviceId) {
            return $query->where('tickets.service_id', $serviceId[0], $serviceId[1]);
        })
        ->when(isset($request->ticketClientId), function ($query) use ($ticketClientId) {
            return $query->where('tickets.ticket_client_id', $ticketClientId[0], $ticketClientId[1]);
        })
        ->when($nominativo, function ($query) use ($nominativo) {
            return $query->where(function ($subQuery) use ($nominativo) {
                $subQuery->where('ticket_clients.firstname', 'LIKE', '%' . $nominativo . '%')
                         ->orWhere('ticket_clients.lastname', 'LIKE', '%' . $nominativo . '%');
            });
        })

        ->when(isset($request->ticketNumber), function ($query) use ($ticketNumber) {
            return $query->where('tickets.ticket_number', $ticketNumber[0], $ticketNumber[1]);
        })
        ->when(isset($request->status), function ($query) use ($status) {
            return $query->where('tickets.status', $status[0], $status[1]);
        })
        ->when(isset($request->userId), function ($query) use ($userId) {
            return $query->where('tickets.worker_id', $userId[0], $userId[1]);
        })
        ->when(isset($request->tipologiaIstanza), function ($query) use ($tipologiaIstanza) {
            return $query->where('tipologiaIstanza.id', $tipologiaIstanza[0], $tipologiaIstanza[1]);
        })
        ->when(isset($request->urgenza), function ($query) use ($urgenza) {
            $urgenzaId = DB::table('parameter_values')->select('description')->where('id', $urgenza[1])->first();
            return $query->where('tickets.urgenza', $urgenza[0], $urgenzaId->description);
        })
        ->when(isset($request->segnalazione), function ($query) use ($segnalazione) {
            return $query->where('segnalazione.id', $segnalazione[0], $segnalazione[1]);
        })
        ->when(isset($request->esito), function ($query) use ($esito) {
            return $query->where('tickets.esito', $esito[0], $esito[1]);
        })
        ->when(isset($request->companyId), function ($query) use ($companyId) {
            return $query->where('contracts.company_id', $companyId[0], $companyId[1]);
        })
        ->when(isset($request->anno), function ($query) use ($anno) {
            foreach ($anno as $value) {
                $query->orWhere('tickets.anno', 'LIKE', '%' . $value . '%');
            }
            return $query;
        })
        ->when(($remain == 1), function ($query) use ($remain) {
            return $query->whereRaw('DATEDIFF(DATE_ADD(notify_date, INTERVAL 60 DAY), NOW()) <= ?', [15]);
        })
        ->when($supendedTicketsMoreThanTwoWeeks, function ($query) {
            return $query->where('tickets.status', 3)
                 ->whereDate('tickets.status_date', '<=', now()->subDays(15));
        })
        //->where('tickets.created_at', $startDate[0], $startDate[1])
        //->where('tickets.end_date', $endDate[0], $endDate[1] )
        //->where('tickets.status', $status[0], $status[1])
        ->whereNull('tickets.deleted_at')
        ->whereNotNull('tickets.urgenza')
        ->orderBy('tickets.updated_at', 'desc')
        //->orderBy('tickets.urgenza', 'desc')
        ->get();
        


        //$client = Client::where('id', '=', $request->query('id'))->with('addresses')->get();
        return response()->json([
            'ticketPage' => new AllTicketCollection(PaginateCollection::paginate($allTickets, $request->pageSize?$request->pageSize:10))
        ], 200);

    }

    public function generateTicketNumber(){

        //DB::table('tickets')->lockForUpdate()->get();

        $oldContractNumber = Ticket::withTrashed()->latest()->sharedLock()->first();

        $newContractNumber = "";

        if($oldContractNumber == null){

            $newContractNumber = "1_" . date('Y');

        } else {

            $newContractNumber =  (((int)explode("_",$oldContractNumber['ticket_number'])[0]) + 1) . "_" . date('Y');

        }

        return $newContractNumber;

    }

    public function createTicket(array $ticketData, array $ticketClientData, array $ticketClientContactData, array $ticketClientAddressData){

        $ticketClientId = $ticketClientData['ticketClientId'];

        if($ticketClientData['ticketClientId'] === "0"){

            $ticketClientId = 0;

        } elseif($ticketClientData['ticketClientId'] === "" || $ticketClientData['ticketClientId'] === null) {

            $ticketClientId = $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);

        }

        /*$ticketClientId = $ticketClientData['ticketClientId'] != ""? $ticketClientData['ticketClientId']: $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);*/

        $afterNotifyDate = null;
        if($ticketData['notifyDate'] != null){
            $date = Carbon::parse($ticketData['notifyDate']);
            $afterNotifyDate = $date->addDays(60);
        }

        $contract = explode("##", $ticketData['contractId']);

        $parameterValue = DB::table('parameter_values')->select('description')->where('description', "0")->first();

        if($ticketData['urgenza']){
            $parameterValue = DB::table('parameter_values')->select('description')->where('id', $ticketData['urgenza'])->first();
        }

        $ticket = Ticket::create([
            'client_id'=> $ticketData['clientId'],
            'contract_id'=> $contract[0],
            'contract_id_2' =>$contract[1],
            'service_id'=> $ticketData['serviceId'],
            'worker_id'=> $ticketData['workerId'] == ""? null : $ticketData['workerId'],
            'ticket_client_id'=> $ticketClientId,
            'connect_type_id' =>$ticketData['connectTypeId']? $ticketData['connectTypeId']: 0,
            'ticket_number' => "",//$ticketData['ticketNumber'],
            'description' => $ticketData['description']? $ticketData['description'] : "",
            'esito' => isset($ticketData['esito'])? $ticketData['esito'] : 0,
            'note' => $ticketData['note']? $ticketData['note'] : "",
            'notify_date'=> $ticketData['notifyDate'],
            'status_date'=> Carbon::now(),
            //'after_notify_date'=> $afterNotifyDate,
            'status'=> "1",
            'anno'=> $ticketData['anno']?implode(",",$ticketData['anno']):"",
            'tipologia_istanza'=>$ticketData['tipologiaIstanza']?? 0,
            'segnalazione'=>$ticketData['segnalazione']?? 0,
            'urgenza' => $parameterValue->description
        ]);

        return $ticket;

    }

    public function editTicket(int $ticketId){

        //$ticket = Ticket::find($ticketId);

        /*if($ticket->status == "2"){
            return response()->json([
                'message' => 'cant be reached!'
            ], 401);
        }*/

        $ticket = DB::table('tickets')->select('tickets.*', DB::raw('(SELECT parameter_values.id FROM parameter_values WHERE parameter_values.description = tickets.urgenza AND parameter_values.deleted_at is null AND parameter_values.parameter_id = 17) AS urgenza'))->where('tickets.id', $ticketId)->first();

        return response()->json(
            new TicketResource($ticket)
        , 200);
    }

    public function updateTicket(array $ticketData, array $ticketClientData, array $ticketClientContactData, array $ticketClientAddressData){

        $ticketClientId = $ticketClientData['ticketClientId'];

        if($ticketClientData['ticketClientId'] === "0"){

            $ticketClientId = 0;

        } elseif($ticketClientData['ticketClientId'] === "" || $ticketClientData['ticketClientId'] === null) {

            $ticketClientId = $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);

        } elseif($ticketClientData['ticketClientId'] >= 1){

            $ticketClientId = $this->ticketClientService->updateTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);
        }

        //$ticketClientId = $ticketClientData['ticketClientId'] != ""? $ticketClientData['ticketClientId']: $this->ticketClientService->createTicketClient($ticketClientData, $ticketClientContactData, $ticketClientAddressData);

        $afterNotifyDate = null;
        if($ticketData['notifyDate'] != null){
            $date = Carbon::parse($ticketData['notifyDate']);
            $afterNotifyDate = $date->addDays(60);
        }

        $contract = explode("##", $ticketData['contractId']);

        $parameterValue = DB::table('parameter_values')->select('description')->where('description', "0")->first();

        if($ticketData['urgenza']){
            $parameterValue = DB::table('parameter_values')->select('description')->where('id', $ticketData['urgenza'])->first();
        }

        $ticket = Ticket::find($ticketData['ticketId']);
       /* $ticket->fill([
            'client_id'=> $ticketData['clientId'],
            'contract_id'=> $contract[0],
            'contract_id_2' =>$contract[1],
            'service_id'=> $ticketData['serviceId'],
            'worker_id'=> $ticketData['workerId'] == ""? null : $ticketData['workerId'],
            'ticket_client_id'=> $ticketClientId,
            'notify_date'=> $ticketData['notifyDate']? $ticketData['notifyDate'] : null,
            //'after_notify_date' => $afterNotifyDate,
            'status'=> $ticketData['status'],
            'description' => $ticketData['description'],
            'connect_type_id' =>$ticketData['connectTypeId'],
            'end_date'=> $ticketData['status'] == "2"? date("Y-m-d H:i:s") : null,
            'closer_id'=> $ticketData['status'] == "2"? Auth::id() : 0,
            'esito' => isset($ticketData['esito'])? $ticketData['esito'] : 0,
            'note' => $ticketData['note']? $ticketData['note'] : "",
            'status_date'=> $ticketData['status'] == $ticket->status?$ticket->status_date:Carbon::now(),
            'anno'=> $ticketData['anno']?implode(",",$ticketData['anno']):"",
            'tipologia_istanza'=>$ticketData['tipologiaIstanza']?? 0,
            'segnalazione'=>$ticketData['segnalazione']?? 0,
            'urgenza' => $parameterValue->description
        ]);*/
        
                        // Only change closer_id if the old status is not 2
        if ( ($ticket->status != "2" && $ticketData['status'] == "2") || $ticket->status != "3" && $ticketData['status'] == "3") {
            $ticket->closer_id = Auth::id();
        }

        $ticket->client_id = $ticketData['clientId'];
        $ticket->contract_id = $contract[0];
        $ticket->contract_id_2 = $contract[1];
        $ticket->service_id = $ticketData['serviceId'];
        $ticket->worker_id = $ticketData['workerId'] == "" ? null : $ticketData['workerId'];
        $ticket->ticket_client_id = $ticketClientId;
        $ticket->notify_date = $ticketData['notifyDate'] ? $ticketData['notifyDate'] : null;
        // $ticket->after_notify_date = $afterNotifyDate; // Uncomment if you have this variable set
        $ticket->status = $ticketData['status'];
        $ticket->description = $ticketData['description'];
        $ticket->connect_type_id = $ticketData['connectTypeId'];
        $ticket->end_date = $ticketData['status'] == "2" ? date("Y-m-d H:i:s") : null;

        $ticket->esito = isset($ticketData['esito']) ? $ticketData['esito'] : 0;
        $ticket->note = $ticketData['note'] ? $ticketData['note'] : "";
        $ticket->status_date = $ticketData['status'] == $ticket->status ? $ticket->status_date : Carbon::now();
        $ticket->anno = $ticketData['anno'] ? implode(",", $ticketData['anno']) : "";
        $ticket->tipologia_istanza = $ticketData['tipologiaIstanza'] ?? 0;
        $ticket->segnalazione = $ticketData['segnalazione'] ?? 0;
        $ticket->urgenza = $parameterValue->description;


        $ticket->save();

        return response()->json([
            'message' => 'ticket main info has been updated!'
        ], 200);

    }

    public function deleteTicket(int $ticketId){
        $ticket = Ticket::find($ticketId);
        $ticket->delete();

        return response()->json([
            'message' => 'ticket has been deleted!'
        ], 200);

    }

}
