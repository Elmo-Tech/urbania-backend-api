<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\TecnicaSecTwo;
use App\Services\Upload\UploadService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TecnicaSecTwoService{


    public function createMultipleTecnicaSecTwo(array $tecnicaSecTwoData, $tecnicaId)
    {

        foreach ($tecnicaSecTwoData as $key => $data) {
            $newTecnicaSecTwoItem = TecnicaSecTwo::create([
                'tipologia' => $data['tipologia'],
                'data_apozione' => $data['dataApozione'],
                'data_approvazione' => $data['dataApprovazione'],
                'pubblicazione_burl' => $data['pubblicazioneBurl'],
                'note' => $data['note'],
                'tecnica_main_data_id'=> $tecnicaId,        
            ]);    
        }

    }

    public function createTecnicaSecTwo(array $tecnicaSecTwoData)
    {

        $newTecnicaSecTwoItem = TecnicaSecTwo::create([
            'tipologia' => $tecnicaSecTwoData['tipologia'],
            'data_apozione' => $tecnicaSecTwoData['dataApozione'],
            'data_approvazione' => $tecnicaSecTwoData['dataApprovazione'],
            'pubblicazione_burl' => $tecnicaSecTwoData['pubblicazioneBurl'],
            'note' => $tecnicaSecTwoData['note'],
            'tecnica_main_data_id'=> $tecnicaSecTwoData['tecnicaMainDataId'],        
        ]);    

        
        return $newTecnicaSecTwoItem;

    }

    public function editTecnicaSecTwo(int $tecnicaSecTwoId)
    {
        
        $newTecnicaSecTwoItem = TecnicaSecTwo::find($tecnicaSecTwoId); 
        
        return $newTecnicaSecTwoItem;
 
    }


    public function updateTecnicaSecTwo(array $tecnicaSecTwoData)
    {

        $updateTecnicaSecTwoItem = TecnicaSecTwo::find($tecnicaSecTwoData['tecnicaSecTwoId']);
        $updateTecnicaSecTwoItem->fill([
            'tipologia' => $tecnicaSecTwoData['tipologia'],
            'data_apozione' => $tecnicaSecTwoData['dataApozione'],
            'data_approvazione' => $tecnicaSecTwoData['dataApprovazione'],
            'pubblicazione_burl' => $tecnicaSecTwoData['pubblicazioneBurl'],
            'note' => $tecnicaSecTwoData['note'],
         ]);

        $updateTecnicaSecTwoItem->save();

        return $updateTecnicaSecTwoItem;
 
    }
 

    public function deleteTecnicaSecTwo(int $tecnicaSecTwoId)
    {

        
        try {
            
            $tecnicaSecTwoItem = TecnicaSecTwo::find($tecnicaSecTwoId);
    
            $tecnicaSecTwoItem->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

    
}