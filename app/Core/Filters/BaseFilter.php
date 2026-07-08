<?php

declare(strict_types=1);

namespace App\Core\Filters;

use App\Core\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseFilter implements FilterInterface
{
    public function __construct(
        protected Request $request
    ) {
    }

    public function apply(Builder $query): Builder
    {
        foreach ($this->filters() as $field => $method) {

            if (!$this->request->filled($field)) {
                continue;
            }

            if (!method_exists($this, $method)) {
                continue;
            }

            $this->{$method}(
                $query,
                $this->request->input($field)
            );
        }

        return $query;
    }

    /**
     * Lista de filtros disponibles.
     */
    abstract protected function filters(): array;
}
