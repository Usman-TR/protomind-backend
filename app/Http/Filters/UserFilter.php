<?php

namespace App\Http\Filters;

use App\Enums\RolesEnum;
use App\Http\Filters\Abstract\QueryFilter;

class UserFilter extends QueryFilter
{
    public function search($search): void
    {
        $this->builder->where('full_name', 'ILIKE', '%' . strtolower($search) . '%');
    }

    public function login($login): void
    {
        $this->builder->where('login', 'ILIKE', '%' . $login . '%');
    }

    public function email($email): void
    {
        $this->builder->where('email', 'ILIKE', '%' . $email . '%');
    }

    public function role($role): void
    {
        $role = RolesEnum::tryFrom($role);

        if(!is_null($role)) {
            $this->builder->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }
    }
}
