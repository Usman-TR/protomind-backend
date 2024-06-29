<?php

namespace App\Http\Filters;

use App\Http\Filters\Abstract\QueryFilter;

class MeetingFilter extends QueryFilter
{
    public function start_date_at($value): void
    {
        $this->builder->whereDate('event_date', '>=', $value);
    }

    public function end_date_at($value): void
    {
        $this->builder->whereDate('event_date', '<=', $value);
    }
}
