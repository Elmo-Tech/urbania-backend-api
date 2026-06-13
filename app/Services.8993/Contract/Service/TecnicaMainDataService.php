<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\TecnicaMainData;
use App\Services\Contract\Service\LavorazioneSecOneService;
use Exception;
use Illuminate\Support\Facades\DB;

class TecnicaMainDataService{

    protected $tecncicaSecOneService;
    protected $tecncicaSecTwoService;

    public function __construct(LavorazioneSecOneService $lavorazioneSecOneService, TecnicaSecOneService $tecncicaSecOneService, TecnicaSecTwoService $tecncicaSecTwoService)
    {
        $this->tecncicaSecOneService = $tecncicaSecOneService;
        $this->tecncicaSecTwoService = $tecncicaSecTwoService;
    }

    public function createTecnicaMainData(array $tecnicaMainData, array $tecnciaSecOneData, array $tecnicaSecTwoData){

        $newTecnica = TecnicaMainData::create([
            'contract_service_id' => $tecnicaMainData['contractServiceId'],
            'note_sec_one'=> $tecnicaMainData['noteSecOne'],
            'tipologia'=> $tecnicaMainData['tipologia'],
            'data_apozione'=> $tecnicaMainData['dataApozione'],
            'data_approvazione' => $tecnicaMainData['dataApprovazione'],
            'pubblicazione_burl' => $tecnicaMainData['pubblicazioneBurl'],
            'note' => $tecnicaMainData['note']
        ]);

        if(isset($tecnciaSecOneData['tecnicaSecOne']) && count($tecnciaSecOneData['tecnicaSecOne']) > 0){

            $optionalData = [
                'tecnicaId' => $newTecnica->id,
                'contractId' => $tecnicaMainData['contractId'],
                'contractServiceId' => $tecnicaMainData['contractServiceId'],
            ];

            $newTecnicaSecOneData = $this->tecncicaSecOneService->createMultipleTecnicaSecOne($tecnciaSecOneData['tecnicaSecOne'], $optionalData);

        }
        
        if(isset($tecnicaSecTwoData['tecnicaSecTwo']) && count($tecnicaSecTwoData['tecnicaSecTwo']) > 0){
            
            $newTecnicaSecTwoData = $this->tecncicaSecTwoService->createMultipleTecnicaSecTwo($tecnicaSecTwoData['tecnicaSecTwo'], $newTecnica->id);

        }


        return $newTecnica;

    }

    public function editTecnicaMainData(int $tecnicaMainDataId)
    {
        
        $tecnicaMainData = TecnicaMainData::where('id', $tecnicaMainDataId)->with('tecnicaSecOne')->with('tecnicaSecTwo')->first();
 
        return $tecnicaMainData;
 
    }


    public function updateTecnicaMainData(array $tecnicaMainData)
    {
        
        $updatedTecnicaMainData = TecnicaMainData::find($tecnicaMainData['tecnicaMainDataId']);
        $updatedTecnicaMainData->fill([
             'note_sec_one'=> $tecnicaMainData['noteSecOne'],
             'tipologia'=> $tecnicaMainData['tipologia'],
             'data_apozione'=> $tecnicaMainData['dataApozione'],
             'data_approvazione' => $tecnicaMainData['dataApprovazione'],
             'pubblicazione_burl' => $tecnicaMainData['pubblicazioneBurl'],
             'note' => $tecnicaMainData['note']
         ]);
        
        $updatedTecnicaMainData->save();
        
        return $updatedTecnicaMainData;
 
    }
    
}