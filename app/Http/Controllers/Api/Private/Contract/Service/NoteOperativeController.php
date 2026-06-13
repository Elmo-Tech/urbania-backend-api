<?php

namespace App\Http\Controllers\Api\Private\Contract\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\NoteOperative\CreateNoteOperativeRequest;
use App\Http\Requests\Contract\NoteOperative\UpdateNoteOperativeRequest;
use App\Http\Resources\Contract\Service\LavorazioneMainDataResource;
use App\Http\Resources\Contract\Service\NoteOperative\AllNoteOperativeResource;
use App\Http\Resources\Contract\Service\NoteOperative\NoteOperativeResource;
use App\Services\Contract\Service\NoteOperativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteOperativeController extends Controller
{
    protected $noteOperativeService;

    public function __construct(NoteOperativeService $noteOperativeService)
    {
        $this->middleware('auth:api');
        $this->noteOperativeService = $noteOperativeService;
    }

    public function index(Request $req){

        $noteOperative = $this->noteOperativeService->allNoteOperatives($req->contractServiceId);

        return response()->json(
            AllNoteOperativeResource::collection($noteOperative)
        );

    }

    public function create(CreateNoteOperativeRequest $createNoteOperativeRequest){

       try {
        DB::beginTransaction();
        $noteOperative = $this->noteOperativeService->createNoteOperative($createNoteOperativeRequest->validated());
        DB::commit();
        return response()->json([
            'message' => 'note operative has been created!'
        ]);
       } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
       }

    }

    public function edit(Request $req){

        $noteOperative =  $this->noteOperativeService->editNoteOperative($req->noteOperativeId);

        return response()->json(
            new NoteOperativeResource($noteOperative)
           // $serviceMainData
        , 200);

    }

    public function update(UpdateNoteOperativeRequest $updateNoteOperativeRequest){

       try {
        DB::beginTransaction();
        $noteOperative = $this->noteOperativeService->updateNoteOperative($updateNoteOperativeRequest->validated());
        DB::commit();
        return response()->json([
            'message' => 'note operative has been updated!'
            ]);
       } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
       }
    }

    public function delete(Request $req){

        $this->noteOperativeService->deleteNoteOperative($req->noteOperativeId);
        return response()->json([
            'message' => 'note operative has been deleted!'
            ], 200);
    }


}
