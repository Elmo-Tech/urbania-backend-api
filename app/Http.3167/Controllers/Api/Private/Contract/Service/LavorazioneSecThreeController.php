<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateLavorazioneSecThreeRequest;
use App\Http\Requests\Contract\ContractService\UpdateLavorazioneSecThreeRequest;
use App\Http\Resources\Contract\Service\LavorazioneSecThreeResource;
use App\Services\Contract\Service\LavorazioneSecThreeService;
use Illuminate\Http\Request;

class LavorazioneSecThreeController extends Controller
{
    protected $lavorazioneSecThreeService;

    public function __construct(LavorazioneSecThreeService $lavorazioneSecThreeService)
    {
        $this->middleware('auth:api');
        $this->lavorazioneSecThreeService = $lavorazioneSecThreeService;
    }

    public function create(CreateLavorazioneSecThreeRequest $createLavorazioneSecThreeRequest){

        $lavorazioneSecThree = $this->lavorazioneSecThreeService->createLavorazioneSecThree($createLavorazioneSecThreeRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec three item has been created!'
        ], 200);

    }

    public function edit(Request $req){

        $lavorazioneSecThree =  $this->lavorazioneSecThreeService->editLavorazioneSecThree($req->lavorazioneSecthreeId);

        return response()->json(
            new LavorazioneSecThreeResource($lavorazioneSecThree)
        , 200);

    }

    public function update(UpdateLavorazioneSecThreeRequest $updateLavorazioneSecThreeRequest){

        $lavorazioneSecthree = $this->lavorazioneSecThreeService->updateLavorazioneSecThree($updateLavorazioneSecThreeRequest->validated());

        return response()->json([
            'message' => 'scheda lavorzione sec three item has been updated!'
        ], 200);

    }

    public function delete(Request $req){

        $this->lavorazioneSecThreeService->deleteLavorazioneSecThree($req->lavorazioneSecThreeId);

        return response()->json([
            'message' => 'scheda lavorzione sec three item has been deleted!'
        ], 200);

    }
}
