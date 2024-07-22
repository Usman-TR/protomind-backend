<?php

namespace App\Http\Filters\Traits;

use App\Http\Filters\Abstract\QueryFilter;
use App\Http\Filters\EquipmentTypeFilter;
use Illuminate\Database\Eloquent\Builder;

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
