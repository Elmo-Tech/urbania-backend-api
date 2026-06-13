<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateLavorazioneSecTwoRequest;
use App\Http\Requests\Contract\ContractService\UpdateLavorazioneSecTwoRequest;
use App\Http\Resources\Contract\Service\LavorazioneSecTwoResource;
use App\Services\Contract\Service\LavorazioneSecTwoService;
use Illuminate\Http\Request;

class LavorazioneSecTwoController extends Controller
{
    protected $lavorazioneSecTwoService;

    public function __construct(LavorazioneSecTwoService $lavorazioneSecTwoService)
    {
        $this->middleware('auth:api');
        $this->lavorazioneSecTwoService = $lavorazioneSecTwoService;
    }

    public function create(CreateLavorazioneSecTwoRequest $createLavorazioneSecTwoRequest){

        $lavorazioneSecTwo = $this->lavorazioneSecTwoService->createLavorazioneSecTwo($createLavorazioneSecTwoRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec two item has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $lavorazioneSecTwo =  $this->lavorazioneSecTwoService->editLavorazioneSecTwo($req->lavorazioneSecTwoId);

        return response()->json(
            new LavorazioneSecTwoResource($lavorazioneSecTwo)
        , 200);

    }

    public function update(UpdateLavorazioneSecTwoRequest $updateLavorazioneSecTwoRequest){

        $lavorazioneSecTwo = $this->lavorazioneSecTwoService->updateLavorazioneSecTwo($updateLavorazioneSecTwoRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec two item has been updated!'
        ], 200);

    }

    public function delete(Request $req){

        $this->lavorazioneSecTwoService->deleteLavorazioneSecTwo($req->lavorazioneSecTwoId);

        return response()->json([
            'message' => 'scheda lavorzione sec two item has been deleted!'
        ], 200);

    }
}
