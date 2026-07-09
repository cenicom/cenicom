<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait HasJsonConversion
{
    /**
     * Convierte el DTO a una cadena JSON.
     *
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode(
            $this->toArray(),
            JSON_THROW_ON_ERROR
        );
    }
}
