<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateSchedaLavorazioneMainDataRequest;
use App\Http\Requests\Contract\ContractService\CreateMultipleLavorazioneSecOneRequest;
use App\Http\Requests\Contract\ContractService\CreateMultipleLavorazioneSecThreeRequest;
use App\Http\Requests\Contract\ContractService\CreateMultipleLavorazioneSecTwoRequest;
use App\Http\Requests\Contract\ContractService\CreateMultipleLavorazioneSecFourRequest;

use App\Http\Requests\Contract\ContractService\UpdateSchedaLavorazioneMainDataRequest;
use App\Http\Resources\Contract\Service\LavorazioneMainDataResource;
use App\Services\Contract\Service\LavorazioneMainDataService;
use Illuminate\Http\Request;


class LavorazioneMainDataController extends Controller
{
    protected $lavorazioneMainDataService;

    public function __construct(LavorazioneMainDataService $lavorazioneMainDataService)
    {
        $this->middleware('auth:api');
        $this->lavorazioneMainDataService = $lavorazioneMainDataService;
    }

    public function create(CreateSchedaLavorazioneMainDataRequest $createSchedaLavorazioneMainDataRequest, CreateMultipleLavorazioneSecOneRequest $createMultipleLavorazioneSecOneRequest, CreateMultipleLavorazioneSecTwoRequest $createMultipleLavorazioneSecTwoRequest, CreateMultipleLavorazioneSecThreeRequest $createMultipleLavorazioneSecThreeRequest, CreateMultipleLavorazioneSecFourRequest $createMultipleLavorazioneSecFourRequest){

        $serviceMainData = $this->lavorazioneMainDataService->createLavorazioneMainData($createSchedaLavorazioneMainDataRequest->validated(), $createMultipleLavorazioneSecOneRequest->validated(), $createMultipleLavorazioneSecTwoRequest->validated(), $createMultipleLavorazioneSecThreeRequest->validated(), $createMultipleLavorazioneSecFourRequest->validated());

        return response()->json([
            'message' => 'service main data has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $serviceMainData =  $this->lavorazioneMainDataService->editLavorazioneMainData($req->lavorazioneMainDataId);

        return response()->json(
            new LavorazioneMainDataResource($serviceMainData)
           // $serviceMainData
        , 200);

    }

    public function update(UpdateSchedaLavorazioneMainDataRequest $updateSchedaLavorazioneMainDataRequest){

        $serviceMainData = $this->lavorazioneMainDataService->updateLavorazioneMainData($updateSchedaLavorazioneMainDataRequest->validated());

        return response()->json([
            'message' => 'service main data has been updated!'
        ], 200);

    }


}
