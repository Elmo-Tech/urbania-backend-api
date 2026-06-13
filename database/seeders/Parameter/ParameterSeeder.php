<?php

namespace Database\Seeders\Parameter;

use App\Models\Parameter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   /**
     * Run the database seeds.
     */
    public function run()
    {
        $parameters = [
            [
                'id' => '1',
                'parameter_name' => 'companyRole',
                'parameter_order' => '1',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '2',
                'parameter_name' => 'addressType',
                'parameter_order' => '2',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '3',
                'parameter_name' => 'companyName',
                'parameter_order' => '3',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '4',
                'parameter_name' => 'serviceType',
                'parameter_order' => '4',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '5',
                'parameter_name' => 'paymentType',
                'parameter_order' => '5',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '6',
                'parameter_name' => 'connectType',
                'parameter_order' => '6',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '7',
                'parameter_name' => 'tipologiaCarico',
                'parameter_order' => '7',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '8',
                'parameter_name' => 'imposta',
                'parameter_order' => '8',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '9',
                'parameter_name' => 'contractParameter',
                'parameter_order' => '9',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '10',
                'parameter_name' => 'lavorazioneSecOneParameter',
                'parameter_order' => '10',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '11',
                'parameter_name' => 'lavorazioneSecTwoParameter',
                'parameter_order' => '11',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '12',
                'parameter_name' => 'tecnicaSecTwoParameter',
                'parameter_order' => '12',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '13',
                'parameter_name' => 'esito',
                'parameter_order' => '13',
                //'created_by' => '1',
                //'updated_by' => '1'
            ],
            [
                'id' => '14',
                'parameter_name' => 'status2',
                'parameter_order' => '14',
                //'created_by' => '1',
                //'updated_by' => '1'
            ]
        ];

        DB::table('parameters')->insert($parameters);
    }
}
