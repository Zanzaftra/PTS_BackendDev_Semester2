<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrcreate([
             'email' => 'admin@gmail.com'], 
             [
             'name' => 'Admin User',
             'password' => \Illuminate\Support\Facades\Hash::make('password'),
             'role' => \App\Models\User::ROLE_ADMIN,
        ]
        );

        \App\Models\User::firstOrcreate([
                'email' => 'user@gmail.com'], 
                [
                'name' => 'Regular User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Models\User::ROLE_USER,
            ]
            );   
    }
}
