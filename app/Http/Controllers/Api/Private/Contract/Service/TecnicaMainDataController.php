<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateMultipleTecnicaSecOneRequest;
use App\Http\Requests\Contract\ContractService\CreateMultipleTecnicaSecTwoRequest;
use App\Http\Requests\Contract\ContractService\CreateTecnicaMainDataRequest;
use App\Http\Requests\Contract\ContractService\UpdateTecnicaMainDataRequest;
use App\Http\Resources\Contract\Service\TecnicaMainDataResource;
use App\Services\Contract\Service\TecnicaMainDataService;
use Illuminate\Http\Request;
class TecnicaMainDataController extends Controller
{
    protected $tecnicaMainDataService;

    public function __construct(TecnicaMainDataService $tecnicaMainDataService)
    {
        $this->middleware('auth:api');
        $this->tecnicaMainDataService = $tecnicaMainDataService;
    }

    public function create(CreateTecnicaMainDataRequest $createTecnicaMainDataRequest, CreateMultipleTecnicaSecOneRequest $createMultipleTecnicaSecOneRequest, CreateMultipleTecnicaSecTwoRequest $createMultipleTecnicaSecTwoRequest){
        
        $serviceMainData = $this->tecnicaMainDataService->createTecnicaMainData($createTecnicaMainDataRequest->validated(), $createMultipleTecnicaSecOneRequest->validated(), $createMultipleTecnicaSecTwoRequest->validated());

        return response()->json([
            'message' => 'tecnica main data has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $serviceMainData =  $this->tecnicaMainDataService->editTecnicaMainData($req->tecnicaMainDataId);

        return response()->json(
            new TecnicaMainDataResource($serviceMainData)
        , 200);

    }

    public function update(UpdateTecnicaMainDataRequest $updateTecnicaMainDataRequest){

        $serviceMainData = $this->tecnicaMainDataService->updateTecnicaMainData($updateTecnicaMainDataRequest->validated());

        return response()->json([
            'message' => 'tecnica main data has been updated!'
        ], 200);

    }

}
