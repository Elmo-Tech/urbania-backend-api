<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\ContractPlusData;
use App\Models\Contract\Service\LavorazioneMainData;
use App\Services\Contract\Service\LavorazioneSecOneService;
use App\Services\Contract\Service\LavorazioneSecTwoService;
use App\Services\Contract\Service\LavorazioneSecThreeService;
use App\Services\Contract\Service\LavorazioneSecFourService;

use App\Utils\PaginateCollection;
use Exception;
use Illuminate\Support\Facades\DB;


class LavorazioneMainDataService{

    protected $lavorazioneSecOneService;
    protected $lavorazioneSecTwoService;
    protected $lavorazioneSecThreeService;
    protected $lavorazioneSecFourService;

    public function __construct(LavorazioneSecOneService $lavorazioneSecOneService, LavorazioneSecTwoService $lavorazioneSecTwoService, LavorazioneSecThreeService $lavorazioneSecThreeService, LavorazioneSecFourService $lavorazioneSecFourService)
    {
        $this->lavorazioneSecOneService = $lavorazioneSecOneService;
        $this->lavorazioneSecTwoService = $lavorazioneSecTwoService;
        $this->lavorazioneSecThreeService = $lavorazioneSecThreeService;
        $this->lavorazioneSecFourService = $lavorazioneSecFourService;

    }

    public function createLavorazioneMainData(array $serviceMainData, array $lavorazioneSecOneData, array $lavorazioneSecTwoData, array $lavorazioneSecThreeData, array $lavorazioneSecFourData){

        $newLavorazione = LavorazioneMainData::create([
            'contract_service_id' => $serviceMainData['contractServiceId'],
            'note_sec_one'=> $serviceMainData['noteSecOne'],
            'note_sec_two'=> $serviceMainData['noteSecTwo'],
            'note_sec_three'=> $serviceMainData['noteSecThree'],
             'note_sec_four'=> $serviceMainData['noteSecFour'],
            'imposta'=> $serviceMainData['imposta'],
            'note' => $serviceMainData['note'],
            'n_avvisi' => $serviceMainData['nAvvisi'],
            'importa' => $serviceMainData['importa'],
            'anno_ennissone' => $serviceMainData['annoEnnissone'],
            'anno_accertamento' => $serviceMainData['annoAccertamento']
        ]);

        if(isset($lavorazioneSecOneData['lavorazioneSecOne']) && count($lavorazioneSecOneData['lavorazioneSecOne']) > 0){

            $newlavorazioneSecOneData = $this->lavorazioneSecOneService->createMultipleLavorazioneSecOne($newLavorazione->id, $lavorazioneSecOneData['lavorazioneSecOne']);

        }
    
        if(isset($lavorazioneSecTwoData['lavorazioneSecTwo']) && count($lavorazioneSecTwoData['lavorazioneSecTwo']) > 0){

            $newlavorazioneSecTwoData = $this->lavorazioneSecTwoService->createMultipleLavorazioneSecTwo($newLavorazione->id, $lavorazioneSecTwoData['lavorazioneSecTwo']);

        }

        if(isset($lavorazioneSecThreeData['lavorazioneSecThree']) && count($lavorazioneSecThreeData['lavorazioneSecThree']) > 0){

            $newlavorazioneSecThreeData = $this->lavorazioneSecThreeService->createMultipleLavorazioneSecThree($newLavorazione->id, $lavorazioneSecThreeData['lavorazioneSecThree']);

        }
        
        if(isset($lavorazioneSecFourData['lavorazioneSecFour']) && count($lavorazioneSecFourData['lavorazioneSecFour']) > 0){

            $newlavorazioneSecFourData = $this->lavorazioneSecFourService->createMultipleLavorazioneSecFour($newLavorazione->id, $lavorazioneSecFourData['lavorazioneSecFour']);

        }


    
        return $newLavorazione;

    }

    public function editLavorazioneMainData(int $lavorazioneMainDataId)
    {
        
        $serviceMainData = LavorazioneMainData::where('id', $lavorazioneMainDataId)->with('lavorazioneSecOne')->with('lavorazioneSecTwo')->with('lavorazioneSecThree')->with('contractSportello')->first();
        
        return $serviceMainData;
 
    }


    public function updateLavorazioneMainData(array $serviceMainData)
    {
        
        $updatedServiceMainData = LavorazioneMainData::find($serviceMainData['lavorazioneMainDataId']);
        $updatedServiceMainData->fill([
             'contract_service_id' => $serviceMainData['contractServiceId'],
             'note_sec_one'=> $serviceMainData['noteSecOne'],
             'note_sec_two'=> $serviceMainData['noteSecTwo'],
            'note_sec_three'=> $serviceMainData['noteSecThree'],
             'note_sec_four'=> $serviceMainData['noteSecFour'],
             'imposta'=> $serviceMainData['imposta'],
             'note' => $serviceMainData['note'],
             'n_avvisi' => $serviceMainData['nAvvisi'],
             'importa' => $serviceMainData['importa'],
             'anno_ennissone' => $serviceMainData['annoEnnissone'],
             'anno_accertamento' => $serviceMainData['annoAccertamento']
        ]);
        
        $updatedServiceMainData->save();
        
        return $updatedServiceMainData;
 
    }
    
}