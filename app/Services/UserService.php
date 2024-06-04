<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        $user = auth()->user();

        if ($user->hasRole([RolesEnum::ADMIN->value, RolesEnum::MANAGER->value])) {
            $data['password'] = Hash::make($data['password']);
            $newUser = User::create($data);

            $role = Role::firstOrCreate(['name' => RolesEnum::SECRETARY->value, 'guard_name' => 'api']);

            if ($user->hasRole('admin')) {
                $role = Role::firstOrCreate(['name' => RolesEnum::MANAGER->value, 'guard_name' => 'api']);
            }

            $newUser->assignRole($role->name);
        }
    }
}
