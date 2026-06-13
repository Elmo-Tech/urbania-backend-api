<?php

namespace App\Http\Controllers\Api\Private\ClientOuterTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientOuterTicket\CreateClientOuterTicketRequest;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Services\ClientOuterTicket\ClientOuterTicketService;
use App\Services\Upload\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ClientOuterTicketController extends Controller
{
    protected $clientOuterTicketService;
    protected $uploadService;

    public function __construct(ClientOuterTicketService $clientOuterTicketService, UploadService $uploadService)
    {
        //$this->middleware('auth:api');
        $this->clientOuterTicketService = $clientOuterTicketService;
        $this->uploadService = $uploadService;
    }


    public function create(CreateClientOuterTicketRequest $createClientOuterTicketRequest){

        try {

            DB::beginTransaction();

            $clientOuterTicket = $this->clientOuterTicketService->createClientOuterTicket($createClientOuterTicketRequest->validated());

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

    public function update(Request $request)
    {

        try {

            DB::beginTransaction();

            $ticket = Ticket::where('id', $request->ticketId)->where('email_token', $request->token)->first();

            if($ticket->status == 2){
                return response()->json([
                    'message' => 'ticket has been closed !',
                ], 200);
            }

            $ticket->description = $ticket->description == ""? $request->message: $ticket->description." by client: ".$request->message;

            $ticket->status = 1;

            $ticket->save();

            foreach($request->files as $file){
                $this->uploadService->uploadFile([
                    'file' => $file[0]['path'],
                    'uploadPath' => $request->uploadPath
                ]);
            }


            DB::commit();

            return response()->json([
                'message' => 'ticket has been updated !',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
