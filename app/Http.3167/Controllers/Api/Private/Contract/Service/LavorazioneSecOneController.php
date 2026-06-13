<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateLavorazioneSecOneRequest;
use App\Http\Requests\Contract\ContractService\UpdateLavorazioneSecOneRequest;
use App\Http\Resources\Contract\Service\LavorazioneSecOneResource;
use App\Services\Contract\Service\LavorazioneSecOneService;
use Illuminate\Http\Request;

class LavorazioneSecOneController extends Controller
{
    protected $lavorazioneSecOneService;

    public function __construct(LavorazioneSecOneService $lavorazioneSecOneService)
    {
        $this->middleware('auth:api');
        $this->lavorazioneSecOneService = $lavorazioneSecOneService;
    }

    public function create(CreateLavorazioneSecOneRequest $createLavorazioneSecOneRequest){

        $lavorazioneSecOne = $this->lavorazioneSecOneService->createLavorazioneSecOne($createLavorazioneSecOneRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec on item has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $lavorazioneSecOne =  $this->lavorazioneSecOneService->editLavorazioneSecOne($req->lavorazioneSecOneId);

        return response()->json(
            new LavorazioneSecOneResource($lavorazioneSecOne)
        , 200);

    }

    public function update(UpdateLavorazioneSecOneRequest $updateLavorazioneSecOneRequest){

        $lavorazioneSecOne = $this->lavorazioneSecOneService->updateLavorazioneSecOne($updateLavorazioneSecOneRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec on item has been updated!'
        ], 200);

    }

    public function delete(Request $req){

        $this->lavorazioneSecOneService->deleteLavorazioneSecOne($req->lavorazioneSecOneId);

        return response()->json([
            'message' => 'scheda lavorzione sec on item has been deleted!'
        ], 200);

    }

}
