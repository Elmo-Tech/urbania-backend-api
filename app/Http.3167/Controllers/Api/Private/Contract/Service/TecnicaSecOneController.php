<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateTecnicaSecOneRequest;
use App\Http\Requests\Contract\ContractService\UpdateTecnicaImageRequest;
use App\Http\Requests\Contract\ContractService\UpdateTecnicaSecOneRequest;
use App\Http\Resources\Contract\Service\TecnicaSecOneResource;
use App\Services\Contract\Service\TecnicaSecOneService;
use Illuminate\Http\Request;

class TecnicaSecOneController extends Controller
{
    protected $tecnicaSecOneService;

    public function __construct(TecnicaSecOneService $tecnicaSecOneService)
    {
        $this->middleware('auth:api');
        $this->tecnicaSecOneService = $tecnicaSecOneService;
    }

    public function create(CreateTecnicaSecOneRequest $createTecnicaSecOneRequest){

        $tecnicaSecOne = $this->tecnicaSecOneService->createTecnicaSecOne($createTecnicaSecOneRequest->validated());

        return response()->json([
            'message' => 'scheda tecnica sec on item has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $tecnicaSecOne =  $this->tecnicaSecOneService->editTecnicaSecOne($req->tecnicaSecOneId);

        return response()->json(
            new TecnicaSecOneResource($tecnicaSecOne)
        , 200);

    }

    public function update(UpdateTecnicaSecOneRequest $updateTecnicaSecOneRequest){

        $tecnicaSecOne = $this->tecnicaSecOneService->updateTecnicaSecOne($updateTecnicaSecOneRequest->validated());

        return response()->json([
            'message' => 'scheda tecnica on item has been updated!'
        ], 200);

    }

    public function delete(Request $req){

        $this->tecnicaSecOneService->deleteTecnicaSecOne($req->tecnicaSecOneId);

        return response()->json([
            'message' => 'scheda tecnica sec on item has been deleted!'
        ], 200);

    }

}
