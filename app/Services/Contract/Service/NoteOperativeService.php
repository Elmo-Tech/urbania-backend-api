<?php

namespace App\Services\Contract\Service;

use App\Models\Contract\Service\NoteOperative;
use Exception;
use Illuminate\Support\Facades\DB;

class NoteOperativeService{

    public function allNoteOperatives(int $contractServiceId){

        $allContractSportello = NoteOperative::where('contract_service_id', $contractServiceId)->get();

        return $allContractSportello;

    }

    public function createNoteOperative(array $lavorazioneSecFiveData){

       $lavorazioneSecFiveItem = NoteOperative::create([
            'contract_service_id'=> $lavorazioneSecFiveData['contractServiceId'],
            'description'=> $lavorazioneSecFiveData['description']??null,
            'date'=> $lavorazioneSecFiveData['date']??null,
        ]);

        return $lavorazioneSecFiveItem;

    }

    public function editNoteOperative(int $lavorazioneSecFiveId){

        $lavorazioneSecFiveItem = NoteOperative::find($lavorazioneSecFiveId);

        return $lavorazioneSecFiveItem;

    }


    public function updateNoteOperative(array $lavorazioneSecFiveData)
    {

        $lavorazioneSecFiveItem = NoteOperative::find($lavorazioneSecFiveData['noteOperativeId']);
        $lavorazioneSecFiveItem->fill([
            'description'=> $lavorazioneSecFiveData['description']??null,
            'date'=> $lavorazioneSecFiveData['date']??null,
         ]);

         $lavorazioneSecFiveItem->save();

         return $lavorazioneSecFiveItem;

    }


    public function deleteNoteOperative(int $lavorazioneSecFiveId)
    {


        try {

            $lavorazioneSecFiveItem = NoteOperative::find($lavorazioneSecFiveId);

            $lavorazioneSecFiveItem->delete();

        } catch (\Throwable $th) {

            throw new Exception('error.');

        }

    }


}
