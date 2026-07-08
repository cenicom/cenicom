<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait HasArrayConversion
{
    /**
     * Convertir DTO a array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
