<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Calender\CalenderSeeder;
use Database\Seeders\Parameter\ParameterSeeder;
use Database\Seeders\Parameter\ParameterValuesSeeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Event\EventSeeder;
use Database\Seeders\Role\RolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
            //UserSeeder::class,
            //ParameterSeeder::class,
            //ParameterValuesSeeder::class,
            //CalenderSeeder::class,
            //EventSeeder::class
        ]);
    
    }
}
