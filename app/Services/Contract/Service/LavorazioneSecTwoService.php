<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\LavorazioneSecTwo;
use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class LavorazioneSecTwoService{

    public function createMultipleLavorazioneSecTwo(int $lavorazioneId, array $lavorazioneSecTwoData)
    {
        

        foreach ($lavorazioneSecTwoData as $key => $data) {
            $years = implode("#", $data['years']);
            $yearsValues = implode("#", $data['yearsValues']);
            
            $newlavorazioneSecTwoItem = LavorazioneSecTwo::create([
                'description' => $data['description'],
                'years' => count($data['years'])? $years:"",
                'years_values'=> count($data['yearsValues'])? $yearsValues:"",
                'lavorazione_sec_two_parameter_id'=> $data['lavorazioneSecTwoParameterId']?$data['lavorazioneSecTwoParameterId']:0,
                'lavorazione_main_data_id'=> $lavorazioneId
            ]);
    
        }

    }

    public function createLavorazioneSecTwo(array $lavorazioneSecTwoData)
    {
        $years = implode("#", $lavorazioneSecTwoData['years']);
        $yearsValues = implode("#", $lavorazioneSecTwoData['yearsValues']);

       $newlavorazioneSecTwoItem = LavorazioneSecTwo::create([
            'description' => $lavorazioneSecTwoData['description'],
            'years' => count($lavorazioneSecTwoData['years'])? $years:"",
            'years_values'=> count($lavorazioneSecTwoData['yearsValues'])? $yearsValues:"",
            'lavorazione_sec_two_parameter_id'=> $lavorazioneSecTwoData['lavorazioneSecTwoParameterId']?$lavorazioneSecTwoData['lavorazioneSecTwoParameterId']:0,
            'lavorazione_main_data_id'=> $lavorazioneSecTwoData['lavorazioneMainDataId']
        ]);

        return $newlavorazioneSecTwoItem;

    }

    public function editLavorazioneSecTwo(int $lavorazioneSecTwoId)
    {
        
        $lavorazioneSecTwoItem = LavorazioneSecTwo::find($lavorazioneSecTwoId);
        
        return $lavorazioneSecTwoItem;
 
    }


    public function updateLavorazioneSecTwo(array $lavorazioneSecTwoData){
        
        $years = implode("#", $lavorazioneSecTwoData['years']);
        $yearsValues = implode("#", $lavorazioneSecTwoData['yearsValues']);

        $updatelavorazioneSecTwoItem = LavorazioneSecTwo::find($lavorazioneSecTwoData['lavorazioneSecTwoId']);
        $updatelavorazioneSecTwoItem->fill([
             'description' => $lavorazioneSecTwoData['description'],
             'years' => count($lavorazioneSecTwoData['years'])? $years:"",
             'years_values'=> count($lavorazioneSecTwoData['yearsValues'])? $yearsValues:"",
             'lavorazione_sec_two_parameter_id'=> $lavorazioneSecTwoData['lavorazioneSecTwoParameterId']?$lavorazioneSecTwoData['lavorazioneSecTwoParameterId']:0,
             //'lavorzione_main_data_id'=> $lavorazioneSecTwoData['lavorzioneMainDataId'],
         ]);

        $updatelavorazioneSecTwoItem->save();
 
        return $updatelavorazioneSecTwoItem;
 
     }
 

    public function deleteLavorazioneSecTwo(int $lavorazioneSecTwoId)
    {

        
        try {
            
            $lavorazioneSecTwoItem = LavorazioneSecTwo::find($lavorazioneSecTwoId);
    
            $lavorazioneSecTwoItem->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

    
}