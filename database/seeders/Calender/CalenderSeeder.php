<?php

namespace Database\Seeders\Calender;

use App\Models\Event\Calender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CalenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Calender::create([
            'id' => '1',
            'label' => 'cal1',
            'description' => 'desc',
            'color' => '#f00',
            'created_by' => '1',
            'updated_by' => '1',
        ]);
    }
}
