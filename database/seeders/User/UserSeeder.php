<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'id' => '1',
            'firstname' => 'mo',
            'lastname' => 'elhadad',
            'username' => 'admin',
            'email' => 'elhadad',
            'status' => '1',
            'password' => Hash::make('Urbania23.'),
        ]);

        $role = Role::findByName('superAdmin');

        $user->assignRole($role);

    }
}
