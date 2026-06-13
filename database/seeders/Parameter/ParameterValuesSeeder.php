<?php

namespace Database\Seeders\Parameter;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parameters = [
            [
                'id' => '1',
                'parameter_value' => 'ceo',
                'description' => '',
                'parameter_order' => '1',
                'parameter_id' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '2',
                'parameter_value' => 'employee',
                'description' => '',
                'parameter_order' => '1',
                'parameter_id' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '3',
                'parameter_value' => 'main address',
                'description' => '',
                'parameter_order' => '2',
                'parameter_id' => '2',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '4',
                'parameter_value' => 'urbania1',
                'description' => '',
                'parameter_order' => '3',
                'parameter_id' => '3',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '5',
                'parameter_value' => 'urbania2',
                'description' => '',
                'parameter_order' => '3',
                'parameter_id' => '3',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '6',
                'parameter_value' => 'water service',
                'description' => '',
                'parameter_order' => '4',
                'parameter_id' => '4',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '7',
                'parameter_value' => 'digital service',
                'description' => '',
                'parameter_order' => '4',
                'parameter_id' => '4',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '8',
                'parameter_value' => 'visa',
                'description' => '',
                'parameter_order' => '5',
                'parameter_id' => '5',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '9',
                'parameter_value' => 'cash',
                'description' => '',
                'parameter_order' => '5',
                'parameter_id' => '5',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '10',
                'parameter_value' => 'phone',
                'description' => '',
                'parameter_order' => '6',
                'parameter_id' => '6',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '11',
                'parameter_value' => 'email',
                'description' => '',
                'parameter_order' => '6',
                'parameter_id' => '6',
                'created_by' => '1',
                'updated_by' => '1'
            ]
        ];

        DB::table('parameter_values')->insert($parameters);
    }
}
