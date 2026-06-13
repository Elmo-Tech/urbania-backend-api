<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractService\CreateLavorazioneSecFourRequest;
use App\Http\Requests\Contract\ContractService\UpdateLavorazioneSecFourRequest;
use App\Http\Resources\Contract\Service\LavorazioneSecFourServiceResource;
use App\Models\Contract;
use App\Services\Contract\Service\LavorazioneSecFourService;
use Illuminate\Http\Request;


class LavorazioneSecFourController extends Controller
{
    protected $contractSportelloService;    

    public function __construct(LavorazioneSecFourService $contractSportelloService)
    {
        $this->middleware('auth:api');
        $this->contractSportelloService = $contractSportelloService;
    }

    public function allcontractsportello(Request $req)
    {
        
        $allContractSportello = $this->contractSportelloService->allContractSportello($req->lavorazioneSecTwoId);
        return response()->json(
            ContractSportelloResource::collection($allContractSportello)
        , 200);

    }


    public function create(CreateLavorazioneSecFourRequest $createContractSportelloRequest)
    {

        $contractSportello = $this->contractSportelloService->createContractSportello($createContractSportelloRequest->validated()); 

        return response()->json([
            'message' => 'contract sportello has been created!'
        ], 200);

    }

    public function edit(Request $req)
    {
        
        $contractSportello = $this->contractSportelloService->editContractSportello($req->lavorazioneSecFourId);
        return response()->json(
             new LavorazioneSecFourServiceResource($contractSportello)
        , 200);

    }

    public function update(UpdateLavorazioneSecFourRequest $updateContractSportelloRequest)
    {
        $contractSportello = $this->contractSportelloService->updateContractSportello($updateContractSportelloRequest->validated());

        return response()->json([
            'message' => 'contract sportello has been updated!'
        ], 200);
    }

    public function delete(Request $req){

        $this->contractSportelloService->deleteContractSportello($req->lavorazioneSecFourId);

        return response()->json([
            'message' => 'contract sportello has been deleted!'
        ], 200);


    }
}
