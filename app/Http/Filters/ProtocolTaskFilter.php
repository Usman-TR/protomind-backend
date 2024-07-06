<?php

namespace App\Http\Filters;

use App\Http\Filters\Abstract\QueryFilter;

class ProtocolTaskFilter extends QueryFilter
{
    public function search(string $value): void
    {
        $this->builder->where('essence', 'ILIKE', '%' . $value . '%');
    }


}
