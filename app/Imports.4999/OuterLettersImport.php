<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\OuterLetter\OuterLetter;
use App\Models\ParameterValue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OuterLettersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $clientAcronym = explode('-', $row['codice'])[0];
        $serviceInternalCode = explode('-', $row['codice'])[1];
        //dd($clientAcronym);
        $year = explode('-', $row['codice'])[2];
        //dd($row);

        
        $client = Client::where('name_acronym', $clientAcronym)->first();
        $service = ParameterValue::where('internal_code', $serviceInternalCode)->first();

        return new OuterLetter([
            'letter_id' => $row['racc'],
            'name' => $row['denominazione'],
            'address' => $row['indirizzo'],
            'cap' => $row['cap'],
            'city' => $row['localita'],
            'province' => $row['prov'],
            'internal_code' => $row['codice'],
            'client_id' => $client->id,
            'service_id' => $service->id,
            'year' => $year,
            'cf' => $row['codfisc'],
            'numero' => $row['numero']
        ]);
    }
}
