<?php

namespace App\Http\Filters;

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
}
