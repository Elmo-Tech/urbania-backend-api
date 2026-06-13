<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\ContractSportello;
use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class LavorazioneSecFourService{

    public function allContractSportello(int $contractId){
        
        $allContractSportello = ContractSportello::where('contract_id', $contractId)->get();
 
        return $allContractSportello;
 
    }


    public function createMultipleLavorazioneSecFour(int $lavorazioneId, array $lavorazioneSecFourData)
    {
        

        foreach ($lavorazioneSecFourData as $key => $data) {

            $newlavorazioneSecFourItem = ContractSportello::create([
                'tipologia_sportello'=> $data['tipologiaSportello']??null,
                'data_ins'=> $data['dataIns']??null,
                'n_one'=> $data['nOne']??null,
                'note' => $data['note']??null,
                'worker_id' => $data['workerId']??null,
                'lavorazione_main_data_id'=> $lavorazioneId
            ]);
    
        }

    }


    public function createContractSportello(array $contractSportelloData){
        
       $contractSportello = ContractSportello::create([
            'lavorazione_main_data_id'=> $contractSportelloData['lavorazioneMainDataId'],
            'tipologia_sportello'=> $contractSportelloData['tipologiaSportello']??null,
            'data_ins'=> $contractSportelloData['dataIns']??null,
            'n_one'=> $contractSportelloData['nOne']??null,
            'note' => $contractSportelloData['note']??null,
            'worker_id' => $contractSportelloData['workerId']??null
        ]);

        return $contractSportello;

    }

    public function editContractSportello(int $contractSportelloId){
        
        $contractSportello = ContractSportello::find($contractSportelloId);
 
        return $contractSportello;
 
    }


    public function updateContractSportello(array $contractSportelloData)
    {
        
        $contractSportello = ContractSportello::find($contractSportelloData['lavorazioneSecFourId']);
        $contractSportello->fill([
            'lavorazione_main_data_id'=> $contractSportelloData['lavorazioneMainDataId'],
            'tipologia_sportello'=> $contractSportelloData['tipologiaSportello']??null,
            'data_ins'=> $contractSportelloData['dataIns']??null,
            'n_one'=> $contractSportelloData['nOne']??null,
            'note' => $contractSportelloData['note']??null,
            'worker_id' => $contractSportelloData['workerId']??null
         ]);

         $contractSportello->save();
 
         return $contractSportello;
 
    }
 
    
    public function deleteContractSportello(int $contractSportelloId)
    {

        
        try {
            
            $contractSportello = ContractSportello::find($contractSportelloId);
    
            $contractSportello->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

   
}