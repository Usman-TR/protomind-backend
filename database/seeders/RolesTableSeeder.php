<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => RolesEnum::ADMIN->value, 'guard_name' => 'api']);
        Role::create(['name' => RolesEnum::MANAGER->value, 'guard_name' => 'api']);
        Role::create(['name' => RolesEnum::SECRETARY->value, 'guard_name' => 'api']);
        Role::create(['name' => RolesEnum::EXTERNAL->value, 'guard_name' => 'api']);
    }
}
