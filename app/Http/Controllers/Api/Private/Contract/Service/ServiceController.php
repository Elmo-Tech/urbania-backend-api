<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\CreateSingleContractServiceRequest;
use App\Http\Requests\Contract\UpdateSingleContractServiceRequest;
use App\Services\Contract\ContractServiceParamService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $contractService;

    public function __construct(ContractServiceParamService $contractService)
    {
        $this->middleware('auth:api');
        $this->contractService = $contractService;
    }

    public function create(CreateSingleContractServiceRequest $req){

        return $this->contractService->createSingleContractService($req->validated());
    }


    public function edit(Request $req){

        return $this->contractService->EditSingleContractService($req->contractId, $req->serviceId);
    }

    public function update(UpdateSingleContractServiceRequest $req){
        
        return $this->contractService->updateSingleContractService($req->validated());
    }

    public function delete(Request $req){
        
        return $this->contractService->deleteSingleContractService($req->contractId, $req->serviceId);
    }

}
