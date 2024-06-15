<?php

namespace App\Http\Filters\Abstract;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     */
    public function apply(Builder $builder): void
    {
        $this->builder = $builder;

        foreach (request()->query() as $method => $value) {
            if (method_exists($this, $method) && $value !== null) {
                call_user_func_array([$this, $method], (array)$value);
            }
        }

        $this->applyDefaultFilters();
    }

    protected function applyDefaultFilters(): void
    {
        //
    }

}
