<?php

namespace App\Http\Filters\Traits;

use App\Http\Filters\Abstract\QueryFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * @param Builder $builder
     * @param QueryFilter $filter
     */
    public function scopeFilter(Builder $builder, QueryFilter $filter): void
    {
        $filter->apply($builder);
    }
}
