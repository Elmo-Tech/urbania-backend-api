<?php

namespace Database\Seeders\Event;

use App\Models\Event\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'id' => '1',
                'title' => 'event 1',
                'description' => 'event 1 description',
                'start_date' => '2025-12-12',
                'end_date' => '2027-12-12',
                'url' => '',
                'all_day' => '0',
                'calender_id' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'id' => '2',
                'title' => 'event 1',
                'description' => 'event 1 description',
                'start_date' => null,
                'end_date' => null,
                'url' => '',
                'all_day' => '1',
                'calender_id' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ]
            ];
        DB::table('events')->insert($events);
    }
}
