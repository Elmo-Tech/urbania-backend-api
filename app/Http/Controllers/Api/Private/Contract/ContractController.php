<?php

namespace App\Http\Controllers\Api\Private\Contract;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\CreateContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Requests\Contract\CreateContractParamServiceRequest;
use App\Services\Contract\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->middleware('auth:api');
        $this->contractService = $contractService;
    }
    public function allcontracts(Request $request)
    {
        return $this->contractService->allcontracts($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateContractRequest $contractReq, CreateContractParamServiceRequest $contractServiceReq)
    {
        return $this->contractService->createContract($contractReq->validated(), $contractServiceReq->validated());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        return $this->contractService->editContract($request->contractId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request)
    {
        return $this->contractService->updateContract($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        return $this->contractService->deleteContract($request->contractId);
    }
}
