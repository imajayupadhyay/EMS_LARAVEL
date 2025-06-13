<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ajay Upadhyay',
            'email' => 'admin@ajay.com',
            'password' => Hash::make('Ajay@test'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Ajay Upadhyay',
            'email' => 'manager@ajay.com',
            'password' => Hash::make('Ajay@test'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Ajay Upadhyay',
            'email' => 'employee@ajay.com',
            'password' => Hash::make('Ajay@test'),
            'role' => 'employee',
        ]);
    }
}
