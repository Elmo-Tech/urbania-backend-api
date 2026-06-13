<?php

namespace App\Services\OuterLetter;

use App\Imports\OuterLettersImport;
use App\Models\OuterLetter\OuterLetter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;


class OuterLetterService{

    public function allOuterLetter(array $filters){

        $client = $filters['client']?? null;
        $service = $filters['service']?? null;
        $year = $filters['year']?? null;
        $name = $filters['name']?? null;
        $number = $filters['number']?? null;
        $cf = $filters['code']?? null;
        $receivedDate = $filters['receivedDate']?? null;




        $allOuterLetter = OuterLetter::with('clients', 'services', 'uploadedBy')
            ->when(isset($client), function ($query) use ($client) {
                return $query->where('client_id', '=', $client);
            })
            ->when(isset($service), function ($query) use ($service) {
                return $query->where('service_id', '=', $service);
            })
            ->when(isset($year), function ($query) use ($year) {
                return $query->where('year', '=', $year);
            })
            ->when(isset($name), function ($query) use ($name) {
                return $query->where('name', 'LIKE', "%" . $name . "%");
            })
             ->when(isset($number), function ($query) use ($number) {
                return $query->where('numero', '=', $number);
            })
             ->when(isset($cf), function ($query) use ($cf) {
                return $query->where('cf', '=', $cf);
            })
            ->when(isset($receivedDate), function ($query) use ($receivedDate) {
$startOfDay = Carbon::createFromFormat('d/m/Y', $receivedDate)->startOfDay();
$endOfDay = Carbon::createFromFormat('d/m/Y', $receivedDate)->endOfDay();
                return $query->whereBetween('received_date', [$startOfDay, $endOfDay]);
            })
            ->get();



        return $allOuterLetter;
    }

    public function CreateFromExcelOuterLetter(string $path){
        Excel::import(new OuterLettersImport, $path, 'public');
    }

    public function editOuterLetter(int $outerLetterId)
    {

        $outerLetter = OuterLetter::find($outerLetterId);

        return $outerLetter;
    }

    public function updateOuterLetter(array $outerLetterData)
    {
        $outerLetter = OuterLetter::find($outerLetterData['outerLetterId']);

        $formattedDate = $outerLetterData['receivedDate']?Carbon::createFromFormat('d/m/Y', $outerLetterData['receivedDate'])->format('Y-m-d'):null;


        $outerLetter->letter_id = $outerLetterData['letterId'];
        $outerLetter->name = $outerLetterData['name'];
        $outerLetter->address = $outerLetterData['address'];
        $outerLetter->cap = $outerLetterData['cap'];
        $outerLetter->city = $outerLetterData['city'];
        $outerLetter->province = $outerLetterData['province'];
        $outerLetter->internal_code = $outerLetterData['internalCode'];
        $outerLetter->client_id = $outerLetterData['clientId'];
        $outerLetter->service_id = $outerLetterData['serviceId'];
        $outerLetter->year = $outerLetterData['year'];
        $outerLetter->cf = $outerLetterData['cf'];
        $outerLetter->note = $outerLetterData['note'];
        $outerLetter->is_opened = $outerLetterData['isOpened'];
        $outerLetter->received_date = $formattedDate;
        $outerLetter->position = $outerLetterData['position'];

        if(isset($outerLetterData['numero'])){
            $outerLetter->numero = $outerLetterData['numero'];
        }


        if($outerLetterData['isOpened'] == '1'){
            $outerLetter->opened_date = Carbon::now();
        }


        $outerLetter->save();

        return $outerLetter;
    }

}
