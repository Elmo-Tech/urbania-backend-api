<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\LavorazioneMainData;
use App\Models\Contract\Service\LavorzioneSecThree;
use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class LavorazioneSecThreeService{

    public function createMultipleLavorazioneSecThree(int $lavorazioneId, array $lavorazioneSecThreeData)
    {
        

        foreach ($lavorazioneSecThreeData as $key => $data) {

            $newlavorazioneSecThreeItem = LavorzioneSecThree::create([
                'imposta' => $data['imposta'],
                'note' => $data['note'],
                'n_avvisi' => $data['nAvvisi'],
                'importa' => $data['importa'],
                'anno_ennissone' => $data['annoEnnissone'],
                'anno_accertamento' => $data['annoAccertamento'],
                'lavorazione_main_data_id'=> $lavorazioneId        
            ]);
    
        }

    }

    public function createLavorazioneSecThree(array $lavorazioneSecThreeData)
    {
        $newlavorazioneSecThreeItem = LavorzioneSecThree::create([
            'imposta' => $lavorazioneSecThreeData['imposta'],
            'note' => $lavorazioneSecThreeData['note'],
            'n_avvisi' => $lavorazioneSecThreeData['nAvvisi'],
            'importa' => $lavorazioneSecThreeData['importa'],
            'anno_ennissone' => $lavorazioneSecThreeData['annoEnnissone'],
            'anno_accertamento' => $lavorazioneSecThreeData['annoAccertamento'],
            'lavorazione_main_data_id'=> $lavorazioneSecThreeData['lavorazioneMainDataId']        
        ]);


        return $newlavorazioneSecThreeItem;

    }

    public function editLavorazioneSecThree(int $lavorazioneSecThreeId)
    {
        
        $lavorazioneSecThreeItem = LavorzioneSecThree::find($lavorazioneSecThreeId);
        
        return $lavorazioneSecThreeItem;
 
    }


    public function updateLavorazioneSecThree(array $lavorazioneSecThreeData){
        
        $updatelavorazioneSecThreeItem = LavorzioneSecThree::find($lavorazioneSecThreeData['lavorazioneSecThreeId']);
        $updatelavorazioneSecThreeItem->fill([
            'imposta' => $lavorazioneSecThreeData['imposta'],
            'note' => $lavorazioneSecThreeData['note'],
            'n_avvisi' => $lavorazioneSecThreeData['nAvvisi'],
            'importa' => $lavorazioneSecThreeData['importa'],
            'anno_ennissone' => $lavorazioneSecThreeData['annoEnnissone'],
            'anno_accertamento' => $lavorazioneSecThreeData['annoAccertamento'],
     ]);

        $updatelavorazioneSecThreeItem->save();
 
        return $updatelavorazioneSecThreeItem;
 
     }
 

    public function deleteLavorazioneSecThree(int $lavorazioneSecThreeId)
    {

        
        try {
            
            $lavorazioneSecThreeItem = LavorzioneSecThree::find($lavorazioneSecThreeId);
    
            $lavorazioneSecThreeItem->delete();
            
        } catch (\Throwable $th) {
            
            throw new Exception('error.');

        }

    }

    
}