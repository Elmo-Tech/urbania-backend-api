<?php

namespace App\Services\Select;

use App\Http\Resources\Select\SelectClientContractsResource;
use App\Http\Resources\Select\SelectClientResource;
use App\Http\Resources\Select\SelectTicketClientResource;
use App\Http\Resources\Select\SelectTicketWorkerResource;
use App\Http\Resources\Select\SelectServiceContractResource;
use App\Http\Resources\Select\RolesSelectResource;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Contract\ContractService;
use App\Models\User;
use App\Models\OuterLetter\OuterLetter;
use App\Models\ParameterValue;
use App\Models\TicketClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SelectService{

    public function getClientsSelect(){

        $clients = Client::all();

        return response()->json([
            'clientsSelect' => SelectClientResource::collection($clients)
        ], 200);

    }
    
    public function getUrbaniaClientSelect(){

        $clients = Client::where('urbania_client', 1)->get();

        return response()->json([
            'clientsSelect' => SelectClientResource::collection($clients)
        ], 200);

    }

    public function getClientContractsSelect(int $clientId){

        $contracts = DB::table('contracts')
            ->leftJoin('parameter_values', 'parameter_values.id', '=', 'contracts.company_id')
            ->select('contracts.contract_number', 'contracts.id', 'parameter_values.parameter_value')
            ->where('client_id', $clientId)
            ->where('contracts.deleted_at')
            ->get();


        return response()->json([
            'contractsSelect' => SelectClientContractsResource::collection($contracts)
        ], 200);

    }

    public function getTicketClientSelect(){

        $clients = TicketClient::all();

        return response()->json([
            'ticketClientsSelect' => SelectTicketClientResource::collection($clients)
        ], 200);

    }


    public function getTicketWorkerSelect(){

        $clients = User::all();

        return response()->json([
            'ticketWorkersSelect' => SelectTicketWorkerResource::collection($clients)
        ], 200);

    }

    public function getServiceContractsSelect(int $serviceId, int $clientId){


        $contracts = DB::table('contracts_services')
        ->leftJoin('contracts', 'contracts.id', '=', 'contracts_services.contract_id')
        ->leftJoin('parameter_values', 'parameter_values.id', '=', 'contracts.company_id')
        ->select('contracts_services.id as value', 'contracts.contract_number as label', 'parameter_values.parameter_value as company', 'contracts.id as contract_id')
        ->whereIn('contracts.client_id', [$clientId])
        ->where('contracts_services.service_id', $serviceId)
        ->whereNull('contracts.deleted_at')
        ->get();

        return response()->json([
            'contractsSelect' => SelectServiceContractResource::collection($contracts)//SelectClientResource::collection($clients)
        ], 200);

    }


    public function getParameters(string $parameterOrders){

        $arraOfParam = explode("#", $parameterOrders);
        //dd($arraOfParam);
        $parameters = ParameterValue::whereIn('parameter_order', $arraOfParam)->orderBy('parameter_order')->get();

        $parametersSelect = [];

        foreach ($parameters as $index => $parameter) {

            if(in_array($parameter->parameter_order, $parametersSelect)){
                $parametersSelect[$parameter->parameter_order][] = [
                    'value' => $parameter->id,
                    'label' => $parameter->parameter_value
                ];
            } else {
                $parametersSelect[$parameter->parameter_order][] = [
                    'value' => $parameter->id,
                    'label' => $parameter->parameter_value
                ];
            }
        }

        return response()->json([
            'parametersSelect' => $parametersSelect
        ], 200);

    }

    public function getParametersWithDescription(string $parameterOrders){

        $arraOfParam = explode("#", $parameterOrders);
        //dd($arraOfParam);
        $parameters = ParameterValue::whereIn('parameter_order', $arraOfParam)->whereNotNull('description')->orderBy('parameter_order')->get();

        $parametersSelect = [];

        foreach ($parameters as $index => $parameter) {

            if(in_array($parameter->parameter_order, $parametersSelect)){
                $parametersSelect[$parameter->parameter_order][] = [
                    'value' => $parameter->id,
                    'label' => $parameter->description
                ];
            } else {
                $parametersSelect[$parameter->parameter_order][] = [
                    'value' => $parameter->id,
                    'label' => $parameter->description
                ];
            }
        }

        return response()->json([
            'parametersWithDescriptionSelect' => $parametersSelect
        ], 200);

    }

    public function getClientServices(int $clientId)
    {
        $contracts = Contract::where('client_id', $clientId)->pluck('id')->toArray();

        $contractServices = ContractService::whereIn('contract_id', $contracts)->pluck('service_id')->toArray();


        $parameters = ParameterValue::whereIn('id',$contractServices )->whereNotNull('description')->where('parameter_order', 4)->orderBy('parameter_value')->get();


        $parametersSelect = [];

        foreach ($parameters as $index => $parameter) {

                $parametersSelect[] = [
                    'value' => $parameter->id,
                    'label' => $parameter->description
                ];
        }

        return response()->json([
            'clientServices' => $parametersSelect
        ], 200);

    }

    public function getContractServiceYears(int $contractServiceId){

        $contractServiceYears = ContractService::find($contractServiceId);

        $startDate = Carbon::parse($contractServiceYears->start_date)->year;

        $endDate = Carbon::parse($contractServiceYears->end_date)->year;

        $yearsResult = [];



        for ($yearsCounter=$startDate; $yearsCounter <= $endDate; $yearsCounter++) {
            $yearsResult[] = ["value" => $yearsCounter, "label" => $yearsCounter];
        }

        return $yearsResult;
    }

    public function getUsersSelect(){

        $users = User::select('id as label', 'username as name')->get();

        return $users;
    }

    public function roles()
    {
        $roles = Role::all();

        return RolesSelectResource::collection($roles);
    }

    public function excelTemplate()
    {
        $excelTemplateData = [
            ['value' => 'letterId', 'label' => 'Racc.'],
            ['value' => 'name', 'label' => 'Denominazione'],
            ['value' => 'address', 'label' => 'Indirizzo'],
            ['value' => 'cap', 'label' => 'cap'],
            ['value' => 'city', 'label' => 'Localita\''],
            ['value' => 'province', 'label' => 'Prov.'],
            ['value' => 'internalCode', 'label' => 'Codice'],
            ['value' => 'year', 'label' => 'anno'],
            ['value' => 'note', 'label' => 'note'],
            ['value' => 'clientName', 'label' => 'cliente'],
            ['value' => 'serviceName', 'label' => 'Servizio'],
            ['value' => 'receivedDate', 'label' => 'Data ricezione'],
            ['value' => 'cf', 'label' => 'CF'],
            ['value' => 'numero', 'label' => 'Numero'],
        ];

        return $excelTemplateData;
    }

    public function outerLettersCf()
    {
        $outerLettersCf = OuterLetter::all();

        // Transform the data
        $formattedData = $outerLettersCf->map(function($item) {
            return [
                'value' => $item->cf,
                'label' => $item->cf
            ];
        });

        return $formattedData;
    }

    public function clientCodiceFiscale()
    {
        $clientCodiceFiscale = DB::table('clients')
            ->select(
                DB::raw("COALESCE(NULLIF(cf, ''), NULLIF(trade_register, '')) AS label"),
                'id AS value'
            )
            ->get();
    
        return $clientCodiceFiscale;
    }


}
