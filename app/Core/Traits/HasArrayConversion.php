<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait HasArrayConversion
{
    /**
     * Convierte el DTO en un arreglo asociativo.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
