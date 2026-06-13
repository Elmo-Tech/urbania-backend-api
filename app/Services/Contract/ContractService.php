<?php

namespace App\Services\Contract;
//use App\Http\Requests\Contract\CreateContactRequest;

use App\Http\Resources\Contract\ContractWithServicesResource;
use App\Models\Contract;
use App\Services\Contract\ContractServiceParamService;
use App\Http\Resources\Contract\AllContractCollection;
use App\Utils\PaginateCollection;
use Illuminate\Support\Facades\DB;

class ContractService{

    protected $contractServiceParamService;

    public function __construct(ContractServiceParamService $contractServiceParamService)
    {
        $this->contractServiceParamService = $contractServiceParamService;
    }

    public function allcontracts(Object $request){
        $clientId = $request->clientId? ['=', $request->clientId]:['!=', '0'];
        $companyId = $request->companyId? ['=', $request->companyId]:['!=', '0'];
        $serviceId = $request->serviceId? ['=', $request->serviceId]:null;//['!=', '0'
        
        $fatturazione = $request->fatturazione? ['=', $request->fatturazione]:null;


        //$startDate = $request->startDate? explode("|", $request->startDate) : ["!=", "null"];
        $endDate = $request->endDate? $request->endDate : null;

        $contractNumber = $request->contractNumber? ['LIKE', "%{$request->contractNumber}%"] : ["!=", ""];
        $status = $request->status !== null? ['=', $request->status] : ["!=", null];

       $allContracts = Contract::select('contracts.id', 'contracts.contract_number', 'contracts.start_date', 'contracts.end_date', 'companies.parameter_value as cn', 'clients.company_name', 'contracts.status', DB::raw('GROUP_CONCAT(services.parameter_value) as servicesname'))
            ->leftJoin('parameter_values as companies', 'companies.id', '=', 'contracts.company_id')
            ->leftJoin('clients', 'clients.id', '=', 'contracts.client_id')
            ->leftJoin('contracts_services', 'contracts_services.contract_id', '=', 'contracts.id')
            //->leftJoin('contracts_services', 'contracts_services.contract_id' ,DB::raw('contracts.id AND contracts_services.service_id ' . $serviceId[0] . $serviceId[1]))
            ->leftJoin('parameter_values as services', 'services.id', '=', 'contracts_services.service_id')
            ->where('contract_number', $contractNumber[0], $contractNumber[1])
            ->where('client_id', $clientId[0], $clientId[1])
            ->where('company_id', $companyId[0], $companyId[1])
            ->when(isset($request->serviceId) && $serviceId != null, function($query) use($serviceId){
                return $query->where('contracts_services.service_id', $serviceId[0], $serviceId[1]);
            })
            ->when(isset($request->endDate) && $endDate != null, function($query) use($endDate){
                return $query->whereDate('contracts.end_date', '<',$endDate);
            })
            //->whereDate('start_date', $startDate[0], $startDate[1])
            //->whereDate('end_date', $endDate[0], $endDate[1])
            ->where('status', $status[0], $status[1])
            ->when(isset($request->fatturazione) && $fatturazione != null, function($query) use ($fatturazione) {
                return $query->whereHas('ContractPlusData', function($q) use ($fatturazione) {
                    $q->where('fatturazione', $fatturazione[0], $fatturazione[1]);
                });
            })
            ->groupBy('contracts.id', 'contracts.contract_number', 'contracts.start_date', 'contracts.end_date', 'companies.parameter_value', 'clients.company_name', 'contracts.status')
            ->get();
            
        return response()->json([
            'contractPage' => new AllContractCollection(PaginateCollection::paginate($allContracts, $request->pageSize?$request->pageSize:10))
        ], 200);

    }

    private function generateContractNumber(){

        $oldContractNumber = Contract::withTrashed()->latest()->first();
        $newContractNumber = "";
 
        if($oldContractNumber == null){

            $newContractNumber = "1_" . date('Y');

        } else {

            $newContractNumber =  ((int)explode("_",$oldContractNumber->contract_number)[0] + 1) . "_" . date('Y');

        }

        return $newContractNumber;

    }

    public function createContract(array $contractData, array $contractServiceParamData){
        
       $contract = Contract::create([
            'client_id'=> $contractData['clientId'],
            'company_id'=> $contractData['companyId'],
            'contract_number' => $this->generateContractNumber(),
            'start_date'=> $contractData['startDate'],
            'end_date'=> $contractData['endDate'],
            'cig'=> $contractData['cig']?$contractData['cig']:"",
            'cup'=> $contractData['cup']?$contractData['cup']:"",
            'imposta_id' => $contractData['impostaId'],
            'sign_date' => $contractData['signDate'],
            'note' => $contractData['note']??""
        ]);

        $getContractServicesParam = isset($contractServiceParamData['services'])? $contractServiceParamData['services'] : [];
        
        if(count($getContractServicesParam)){
            $this->contractServiceParamService->attachServiceToContract($contract->id, $getContractServicesParam);
        }
        return response()->json([
            'contractId' => $contract->id,
            'message' => 'contract has been created!'
        ], 200);

    }

    public function editContract(int $contractId){
       

        $contract = Contract::query()->where('id', '=', $contractId)->with('contractService', function($query){

            return $query->with('lavorazioneMainData')->with('tecnicaMainData');

        })->get();
            
        //return $contract;
        return response()->json(
            ContractWithServicesResource::collection($contract)[0]
        , 200);
       
        
    }

    public function updateContract(array $contractData){

        $contract = Contract::find($contractData['contractId']);
        
        $contract->fill([
            'client_id'=> $contractData['clientId'],
            'company_id'=> $contractData['companyId'],
            'start_date'=> $contractData['startDate'],
            'end_date'=> $contractData['endDate'],
            'cig'=> $contractData['cig']?$contractData['cig']:"",
            'cup'=> $contractData['cup']?$contractData['cup']:"",
            'imposta_id' => $contractData['impostaId'],
            'sign_date' => $contractData['signDate'],
            'status'=> $contractData['status'],
            'note' => $contractData['note']??""

        ]);
        
        $contract->save();

        return response()->json([
            'message' => 'contract main info has been updated!'
        ], 200);

    }
    
    
    public function deleteContract(int $contractId){
        $contract = Contract::find($contractId);
        $contract->delete();

        return response()->json([
            'message' => 'contract has been deleted!'
        ], 200);

    }
   
}