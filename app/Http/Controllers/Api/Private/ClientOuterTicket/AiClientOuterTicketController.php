<?php

namespace App\Http\Controllers\Api\Private\ClientOuterTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientOuterTicket\CreateAiClientOuterTicketRequest;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Services\ClientOuterTicket\AiClientOuterTicketService;
use App\Services\Upload\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AiClientOuterTicketController extends Controller
{
    protected $aiClientOuterTicketService;
    protected $uploadService;

    public function __construct(AiClientOuterTicketService $aiClientOuterTicketService, UploadService $uploadService)
    {
        //$this->middleware('auth:api');
        $this->aiClientOuterTicketService = $aiClientOuterTicketService;
        $this->uploadService = $uploadService;
    }


    public function create(CreateAiClientOuterTicketRequest $createClientOuterTicketRequest){

        try {

            DB::beginTransaction();

            $clientOuterTicket = $this->aiClientOuterTicketService->createClientOuterTicket($createClientOuterTicketRequest->validated());

            //Mail::to($clientOuterTicket->email)->send(new TicketCreated());


            DB::commit();

            return response()->json([
                'message' => 'ticket has been created !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
