<?php

declare(strict_types=1);

namespace App\Filters;

use App\Core\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class CurrencyFilter extends BaseFilter
{
    protected function filters(): array
    {
        return [

            'search' => 'search',

            'status' => 'status',

            'default' => 'default',

        ];
    }

    protected function search(
        Builder $query,
        string $value
    ): void {

        $query->where(function ($q) use ($value) {

            $q->where('code', 'like', "%{$value}%")

              ->orWhere('name', 'like', "%{$value}%");

        });

    }

    protected function status(
        Builder $query,
        bool $status
    ): void {

        $query->where('status', $status);

    }

    protected function default(
        Builder $query,
        bool $default
    ): void {

        $query->where('is_default', $default);

    }
}
