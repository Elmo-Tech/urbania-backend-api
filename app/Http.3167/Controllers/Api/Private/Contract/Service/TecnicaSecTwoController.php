<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateTecnicaSecTwoRequest;
use App\Http\Requests\Contract\ContractService\UpdateTecnicaSecTwoRequest;
use App\Http\Resources\Contract\Service\TecnicaSecTwoResource;
use App\Services\Contract\Service\TecnicaSecTwoService;
use Illuminate\Http\Request;

class TecnicaSecTwoController extends Controller
{
    protected $tecnicaSecTwoService;

    public function __construct(TecnicaSecTwoService $tecnicaSecTwoService)
    {
        $this->middleware('auth:api');
        $this->tecnicaSecTwoService = $tecnicaSecTwoService;
    }

    public function create(CreateTecnicaSecTwoRequest $createTecnicaSecTwoRequest){

        $tecnicaSecTwo = $this->tecnicaSecTwoService->createTecnicaSecTwo($createTecnicaSecTwoRequest->validated());

        return response()->json([
            'message' => 'scheda tecnica sec two item has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $tecnicaSecTwo =  $this->tecnicaSecTwoService->editTecnicaSecTwo($req->tecnicaSecTwoId);

        return response()->json(
            new TecnicaSecTwoResource($tecnicaSecTwo)
        , 200);

    }

    public function update(UpdateTecnicaSecTwoRequest $updateTecnicaSecTwoRequest){

        $tecnicaSecTwo = $this->tecnicaSecTwoService->updateTecnicaSecTwo($updateTecnicaSecTwoRequest->validated());

        return response()->json([
            'message' => 'scheda tecnica two item has been updated!'
        ], 200);

    }

    public function delete(Request $req){

        $this->tecnicaSecTwoService->deleteTecnicaSecTwo($req->tecnicaSecTwoId);

        return response()->json([
            'message' => 'scheda tecnica sec two item has been deleted!'
        ], 200);

    }

}
