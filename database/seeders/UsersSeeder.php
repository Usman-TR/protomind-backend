<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'department' => 'global',
            'login' => 'global',
            'password' => Hash::make('test'),
            'email' => 'test@gmail.com',
            'full_name' => 'A B C',
        ])->assignRole('admin');
    }
}
