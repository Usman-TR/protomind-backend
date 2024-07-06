<?php

namespace App\Http\Filters;

use App\Enums\RolesEnum;
use App\Http\Filters\Abstract\QueryFilter;
use App\Scopes\UserScope;

class UserFilter extends QueryFilter
{
    public function search($search): void
    {
        $this->builder->where('full_name', 'ILIKE', '%' . strtolower($search) . '%')
            ->orWhere('email', 'ILIKE', '%' . strtolower($search) . '%');
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

    public function with_blocked($value): void
    {
        $boolean = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($boolean) {
            $this->builder = $this->builder->withoutGlobalScopes();
        }
    }
}
