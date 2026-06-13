<?php

namespace App\Http\Controllers\Api\Private\Select;

use App\Http\Controllers\Controller;
use App\Services\Select\SelectService;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    protected $selectService;

    public function __construct(SelectService $selectService)
    {
        $this->middleware('auth:api');
        $this->selectService = $selectService;
    }

    public function clients(){
        return $this->selectService->getClientsSelect();
    }

    public function ticketclients(){
        return $this->selectService->getTicketClientSelect();
    }

    public function ticketworker(){
        return $this->selectService->getTicketWorkerSelect();
    }

    /*public function contracts(Request $req){
        return $this->selectService->getClientContractsSelect($req->clientId);
    }*/

    public function contracts(Request $req){
        return $this->selectService->getServiceContractsSelect($req->serviceId, $req->clientId);
    }

    public function parameters(Request $req){
        return $this->selectService->getParameters($req->parameters);
    }

    public function contractserviceyears(Request $req){

        return $this->selectService->getContractServiceYears($req->contractServiceId);
    }

    public function users(Request $req){

        return $this->selectService->getUsersSelect();
    }

    public function roles(){

        return $this->selectService->roles();
    }

    public function excelTemplate(){

        return $this->selectService->excelTemplate();
    }
    
    public function outerLetterCf(){
        return $this->selectService->outerLettersCf();
    }


public function clientCodiceFiscale()
{
        return $this->selectService->clientCodiceFiscale();
}




}
