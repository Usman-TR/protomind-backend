<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\ManagerSecretary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    private const ERROR_MESSAGES = [
        'unexpectedRole' => 'Unexpected role',
    ];

    /**
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function create(array $data): User
    {
        $user = auth()->user();

        if(isset($data['external']) && $data['external']) {
            return $this->createExternal($data);
        }

        if ($user->hasRole([RolesEnum::ADMIN->value, RolesEnum::MANAGER->value])) {
            $data['password'] = Hash::make($data['password']);
            $newUser = User::create($data);

            $role = Role::firstOrCreate(['name' => RolesEnum::SECRETARY->value, 'guard_name' => 'api']);

            if ($user->hasRole('admin')) {
                $role = Role::firstOrCreate(['name' => RolesEnum::MANAGER->value, 'guard_name' => 'api']);
            }

            if($user->hasRole('manager')) {
                ManagerSecretary::create([
                    'manager_id' => $user->id,
                    'secretary_id' => $newUser->id,
                ]);
            }

            return $newUser->assignRole($role->name);
        }

        throw new \Exception(self::ERROR_MESSAGES['unexpectedRole']);
    }

    /**
     * @param array $data
     * @return User
     */
    public function createExternal(array $data): User
    {
        if(isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $role = Role::firstOrCreate(['name' => RolesEnum::EXTERNAL->value, 'guard_name' => 'api']);

        return User::create($data)->assignRole($role->name);
    }
}
