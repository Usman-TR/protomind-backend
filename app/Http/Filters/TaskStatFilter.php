<?php

namespace App\Http\Filters;

use App\Http\Filters\Abstract\QueryFilter;
use Carbon\Carbon;




class TaskStatFilter extends QueryFilter
{
    public function date($value): void
    {
        $startDate = Carbon::parse($value)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($value)->endOfMonth()->format('Y-m-d');

        $this->builder->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
    }
}
