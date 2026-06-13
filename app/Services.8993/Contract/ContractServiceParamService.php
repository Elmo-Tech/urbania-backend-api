<?php

namespace App\Services\Contract;

use App\Http\Resources\Contract\SingleServiceResource;
use Illuminate\Support\Facades\DB;
use App\Models\Contract;
use App\Models\ParameterValue;


class ContractServiceParamService{
        
    public function attachServiceToContract($contractId, array $contractServicesData){
        $contract = Contract::find($contractId);
        foreach ($contractServicesData as $key => $contractServiceData) {
            $service = ParameterValue::find($contractServiceData['serviceId']);
            $paymentIds = implode("#", $contractServiceData['paymentIds']);
            $contract->service()->attach($service, [
                'start_date'=> $contractServiceData['startDate']?$contractServiceData['startDate']:null,
                'end_date'=> $contractServiceData['endDate']?$contractServiceData['endDate']:null,
                'payment_id'=> $contractServiceData['paymentIds']?$paymentIds:0,
                'account_number'=> $contractServiceData['accountNumber']?$contractServiceData['accountNumber']:"",
                'carico_id'=> $contractServiceData['caricoId']?$contractServiceData['caricoId']:0,
                'note'=> $contractServiceData['note']?$contractServiceData['note']:""
            ]);
        }
    }

    public function createSingleContractService(array $serviceData){
        $contract = Contract::find($serviceData['contractId']);
        $service = ParameterValue::find($serviceData['serviceId']);
        //$service = $serviceData['serviceId'];
            $paymentIds = isset($serviceData['paymentIds']) && $serviceData['paymentIds'] != null? implode("#", $serviceData['paymentIds']) : [];
            $contract->service()->attach($service, [
                'start_date'=> $serviceData['startDate']?$serviceData['startDate']:null,
                'end_date'=> $serviceData['endDate']?$serviceData['endDate']:null,
                'payment_id'=> $serviceData['paymentIds']?$paymentIds:0,
                'account_number'=> $serviceData['accountNumber']?$serviceData['accountNumber']:"",
                'carico_id'=> $serviceData['caricoId']?$serviceData['caricoId']:0,
                'note'=> $serviceData['note']?$serviceData['note']:""
            ]);

            return response()->json([
                'message' => "service has been added"//serviceResuorce::collection($clientservice)
            ], 200);
    
    }
    
    public function EditSingleContractService(int $contractId, int $serviceId){
        $contract = DB::table('contracts_services')
        ->where('contract_id', $contractId)
        ->where('service_id', $serviceId)
        ->get();
        return response()->json(
            SingleServiceResource::collection($contract)[0]//serviceResuorce::collection($clientservice)
        , 200);

    }

    public function updateSingleContractService(array $serviceData){
        $contract = Contract::find($serviceData['contractId']);
        $service = ParameterValue::find($serviceData['serviceId']);
        $paymentIds = isset($serviceData['paymentIds']) && $serviceData['paymentIds'] != null? implode("#", $serviceData['paymentIds']) : [];
        $contract->service()->updateExistingPivot($service->id, [
            'start_date'=> $serviceData['startDate']?$serviceData['startDate']:"",
            'end_date'=> $serviceData['endDate']?$serviceData['endDate']:"",
            'payment_id'=> $serviceData['paymentIds']?$paymentIds:0,
            'account_number'=> $serviceData['accountNumber']?$serviceData['accountNumber']:"",
            'carico_id'=> $serviceData['caricoId']?$serviceData['caricoId']:0,
            'note'=> $serviceData['note']?$serviceData['note']:""
        ]);

        return response()->json([
            'message' => 'service has been updated!'
        ], 200);

    }

    public function deleteSingleContractService(int $contractId, int $serviceId){
        $contract = Contract::find($contractId);
        $service = ParameterValue::find($serviceId);

        $contract->service()->detach($service->id);

        return response()->json([
            'message' => 'service has been deleted!'
        ], 200);

    }

}