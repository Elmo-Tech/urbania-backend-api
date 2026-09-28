<?php

namespace App\Http\Controllers\Api\Private\ClientOuterTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientOuterTicket\CreateClientOuterTicketRequest;
use App\Http\Requests\ClientOuterTicket\SollecitoRequest;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Services\ClientOuterTicket\ClientOuterTicketService;
use App\Services\Upload\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
            $files = $createClientOuterTicketRequest->file('files', []);

            foreach ($files as $file) {
                $this->uploadService->uploadFile([
                    'file' => $file,
                    'uploadPath' => 'outertickets/' . $clientOuterTicket->id
                ]);
            }

            //Mail::to($clientOuterTicket->email)->send(new TicketCreated());


            DB::commit();

            return response()->json([
                'message' => 'ticket has been created !',
                'clientOuterTicketId' => $clientOuterTicket->id,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function update(SollecitoRequest $request)
    {
        $data = $request->validated();
        $uploadedPaths = [];
        try {
            return DB::transaction(function () use ($data, &$uploadedPaths) {
                $ticket = Ticket::where('id', $data['ticketId'])
                    ->where('email_token', $data['token'])->lockForUpdate()->first();

                abort_unless($ticket, 422, 'Invalid ticket or token.');
                abort_if((int) $ticket->status === 2, 409, 'ticket has been closed !');

                $message = trim((string) ($data['message'] ?? ''));
                if ($message !== '') {
                    $descriptionEntry = now()->format('d/m/Y H:i') . PHP_EOL . $message;
                    $existingDescription = (string) $ticket->description;
                    $ticket->description = $existingDescription === ''
                        ? $descriptionEntry
                        : $existingDescription . PHP_EOL . $descriptionEntry;
                }

                if (array_key_exists('sollecito', $data)) {
                    $urgencyName = $data['sollecito'] ? 'Sollecitato' : 'Non urgente';
                    $preferredId = $data['sollecito'] ? 92 : 91;
                    $options = DB::table('parameter_values')->where('parameter_id', 17)
                        ->whereNull('deleted_at')->whereRaw('LOWER(TRIM(parameter_value)) = ?', [strtolower($urgencyName)]);
                    $urgency = (clone $options)->where('id', $preferredId)->first();
                    if (!$urgency) {
                        $matches = $options->limit(2)->get();
                        abort_unless($matches->count() === 1, 422, $urgencyName . ' urgency is missing or ambiguous.');
                        $urgency = $matches->first();
                    }
                    abort_if($urgency->description === null || trim((string) $urgency->description) === '',
                        422, $urgencyName . ' urgency has no configured value.');
                    $conflictingValue = DB::table('parameter_values')->where('parameter_id', 17)
                        ->whereNull('deleted_at')->where('description', $urgency->description)
                        ->where('id', '!=', $urgency->id)->exists();
                    abort_if($conflictingValue, 422, $urgencyName . ' urgency has an ambiguous configured value.');

                    // Tickets store the option's description; the edit API maps it back to its ID.
                    $ticket->urgenza = $urgency->description;
                }
                $ticket->save();

                foreach ($data['files'] ?? [] as $file) {
                    $path = $this->uploadService->uploadFile([
                        'file' => $file['path'],
                        'uploadPath' => 'tickets/' . $ticket->id,
                        'uniqueName' => true,
                    ]);
                    $uploadedPaths[] = substr($path, strlen('uploads/'));
                }

                return response()->json(['message' => 'ticket has been updated !'], 200);
            });
        } catch (\Throwable $e) {
            // The database transaction cannot undo disk writes; remove only this request's new files.
            if ($uploadedPaths !== []) {
                Storage::disk('uploads')->delete($uploadedPaths);
            }
            throw $e;
        }
    }

}
