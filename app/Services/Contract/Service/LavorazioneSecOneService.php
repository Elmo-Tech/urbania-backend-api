<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\LavorazioneSecOne;
use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class LavorazioneSecOneService{

    public function createMultipleLavorazioneSecOne(int $lavorazioneId, array $lavorazioneSecOneData)
    {
        

        foreach ($lavorazioneSecOneData as $key => $data) {
            $years = implode("#", $data['years']);
            $yearsValues = implode("#", $data['yearsValues']);
            $newlavorazioneSecOneItem = LavorazioneSecOne::create([
                'description' => $data['description'],
                'years' => count($data['years'])? $years:"",
                'years_values'=> count($data['yearsValues'])? $yearsValues:"",
                'lavorazione_sec_one_parameter_id'=> $data['lavorazioneSecOneParameterId']?$data['lavorazioneSecOneParameterId']:0,
                'lavorazione_main_data_id'=> $lavorazioneId,
            ]);
    
        }

    }

    public function createLavorazioneSecOne(array $lavorazioneSecOneData)
    {

        $years = implode("#", $lavorazioneSecOneData['years']);
        $yearsValues = implode("#", $lavorazioneSecOneData['yearsValues']);
       $newlavorazioneSecOneItem = LavorazioneSecOne::create([
            'description' => $lavorazioneSecOneData['description'],
            'years' => count($lavorazioneSecOneData['years'])? $years:"",
            'years_values'=> count($lavorazioneSecOneData['yearsValues'])? $yearsValues:"",
            'lavorazione_sec_one_parameter_id'=> $lavorazioneSecOneData['lavorazioneSecOneParameterId']?$lavorazioneSecOneData['lavorazioneSecOneParameterId']:0,
            'lavorazione_main_data_id'=> $lavorazioneSecOneData['lavorazioneMainDataId'],
        ]);

        return $newlavorazioneSecOneItem;

    }

    public function editLavorazioneSecOne(int $lavorazioneSecOneId)
    {
        
        $lavorazioneSecOneItem = LavorazioneSecOne::find($lavorazioneSecOneId); 
        
        return $lavorazioneSecOneItem;
 
    }


    public function updateLavorazioneSecOne(array $lavorazioneSecOneData)
    {
        $years = implode("#", $lavorazioneSecOneData['years']);
        $yearsValues = implode("#", $lavorazioneSecOneData['yearsValues']);

        $updatelavorazioneSecOneItem = LavorazioneSecOne::find($lavorazioneSecOneData['lavorazioneSecOneId']);
        $updatelavorazioneSecOneItem->fill([
             'description' => $lavorazioneSecOneData['description'],
             'years' => count($lavorazioneSecOneData['years'])? $years:"",
             'years_values'=> count($lavorazioneSecOneData['yearsValues'])? $yearsValues:"",
             'lavorazione_sec_one_parameter_id'=> $lavorazioneSecOneData['lavorazioneSecOneParameterId']?$lavorazioneSecOneData['lavorazioneSecOneParameterId']:0,
             //'lavorzione_main_data_id'=> $lavorazioneSecOneData['lavorzioneMainDataId'],
        ]);

        $updatelavorazioneSecOneItem->save();
 
        return $updatelavorazioneSecOneItem;
 
    }
 

    public function deleteLavorazioneSecOne(int $lavorazioneSecOneId)
    {

        
        try {
            
            $lavorazioneSecOneItem = LavorazioneSecOne::find($lavorazioneSecOneId);
    
            $lavorazioneSecOneItem->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

    
}