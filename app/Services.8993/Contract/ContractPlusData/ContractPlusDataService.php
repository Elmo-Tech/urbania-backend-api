<?php

namespace App\Services\Contract\ContractPlusData;

use App\Models\Contract\ContractPlusData;
use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class ContractPlusDataService{

    public function allContractPlusData(int $contractId){
        
        $allContractPlusData = ContractPlusData::where('contract_id', $contractId)->get();
 
        return $allContractPlusData;
 
    }



    public function createContractPlusData(array $contractPlusData){
        
       $contractPlus = ContractPlusData::create([
            'contract_id'=> $contractPlusData['contractId'],
            'contract_parameter_id'=> $contractPlusData['contractParameterId'],
            'description' => $contractPlusData['description'],
            'fatturazione' => $contractPlusData['fatturazione']
        ]);

        return $contractPlus;

    }

    public function editContractPlusData(int $contractPlusDataId){
        
        $contractPlusData = ContractPlusData::find($contractPlusDataId);
 
        return $contractPlusData;
 
    }


    public function updateContractPlusData(array $contractPlusData)
    {
        
        $contractPlus = ContractPlusData::find($contractPlusData['contractPlusDataId']);
        $contractPlus->fill([
             'contract_id'=> $contractPlusData['contractId'],
             'contract_parameter_id'=> $contractPlusData['contractParameterId'],
             'description' => $contractPlusData['description'],
            'fatturazione' => $contractPlusData['fatturazione']
         ]);

         $contractPlus->save();
 
         return $contractPlus;
 
    }
 
    
    public function deleteContractPlusData(int $contractPlusDataId)
    {

        
        try {
            
            $contractPlus = ContractPlusData::find($contractPlusDataId);
    
            $contractPlus->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

   
}