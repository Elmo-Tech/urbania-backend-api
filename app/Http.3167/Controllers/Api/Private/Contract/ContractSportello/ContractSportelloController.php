<?php

namespace App\Http\Controllers\Api\Private\Contract\ContractSportello;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractSportello\CreateContractSportelloRequest;
use App\Http\Requests\Contract\ContractSportello\UpdateContractSportelloRequest;
use App\Http\Resources\Contract\ContractSportello\ContractSportelloResource;
use App\Models\Contract;
use App\Services\Contract\ContractSportello\ContractSportelloService;
use Illuminate\Http\Request;

class ContractSportelloController extends Controller
{
    protected $contractSportelloService;    

    public function __construct(ContractSportelloService $contractSportelloService)
    {
        $this->middleware('auth:api');
        $this->contractSportelloService = $contractSportelloService;
    }

    public function allcontractsportello(Request $req)
    {
        
        $allContractSportello = $this->contractSportelloService->allContractSportello($req->contractId);
        return response()->json(
            ContractSportelloResource::collection($allContractSportello)
        , 200);

    }


    public function create(CreateContractSportelloRequest $createContractSportelloRequest)
    {

        $contractSportello = $this->contractSportelloService->createContractSportello($createContractSportelloRequest->validated()); 

        return response()->json([
            'message' => 'contract sportello has been created!'
        ], 200);

    }

    public function edit(Request $req)
    {
        
        $contractSportello = $this->contractSportelloService->ediContractSportello($req->contractSportelloId);
        return response()->json(
             new ContractSportelloResource($contractSportello)
        , 200);

    }

    public function update(UpdateContractSportelloRequest $updateContractSportelloRequest)
    {
        $contractSportello = $this->contractSportelloService->updateContractSportello($updateContractSportelloRequest->validated());

        return response()->json([
            'message' => 'contract sportello has been updated!'
        ], 200);
    }

    public function delete(Request $req){

        $this->contractSportelloService->deleteContractSportello($req->contractSportelloId);

        return response()->json([
            'message' => 'contract sportello has been deleted!'
        ], 200);


    }
}
