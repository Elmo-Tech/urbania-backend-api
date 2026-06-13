<?php

namespace App\Http\Controllers\Api\Private\Contract\ContractPlusData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractPlusData\CreateContractPlusDataRequest;
use App\Http\Requests\Contract\ContractPlusData\UpdateContractPlusDataRequest;
use App\Http\Resources\Contract\ContractPlusData\ContractPlusDataResource;
use App\Models\Contract;
use App\Services\Contract\ContractPlusData\ContractPlusDataService;
use Illuminate\Http\Request;

class ContractPlusDataController extends Controller
{
    protected $contractPlusDataService;    

    public function __construct(ContractPlusDataService $contractPlusDataService)
    {
        $this->middleware('auth:api');
        $this->contractPlusDataService = $contractPlusDataService;
    }

    public function allcontractplusdata(Request $req)
    {
        
        $contractPlusData = $this->contractPlusDataService->allContractPlusData($req->contractId);
        return response()->json(
            ContractPlusDataResource::collection($contractPlusData)
        , 200);

    }


    public function create(CreateContractPlusDataRequest $createContractPlusDataRequest)
    {

        $contractPlusData = $this->contractPlusDataService->createContractPlusData($createContractPlusDataRequest->validated());

        return response()->json([
            'message' => 'plus data has been created!'
        ], 200);

    }

    public function edit(Request $req)
    {
        
        $contractPlusData = $this->contractPlusDataService->editContractPlusData($req->contractPlusDataId);
        return response()->json(
             new ContractPlusDataResource($contractPlusData)
        , 200);

    }

    public function update(UpdateContractPlusDataRequest $updateContractPlusDataRequest)
    {
        $contractPlusData = $this->contractPlusDataService->updateContractPlusData($updateContractPlusDataRequest->validated());

        return response()->json([
            'message' => 'plus data has been updated!'
        ], 200);
    }

    public function delete(Request $req){

        $this->contractPlusDataService->deleteContractPlusData($req->contractPlusDataId);

        return response()->json([
            'message' => 'plus data has been deleted!'
        ], 200);


    }
}
