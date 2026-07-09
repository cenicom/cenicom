<?php

declare(strict_types=1);

namespace App\Core\DTO;

use App\Core\Contracts\DTOInterface;
use App\Core\Traits\HasArrayConversion;
use App\Core\Traits\HasJsonConversion;
use JsonSerializable;

abstract class BaseDTO implements DTOInterface, JsonSerializable
{
    use HasArrayConversion;
    use HasJsonConversion;

    /**
     * Cada DTO conoce cómo construirse.
     */
    abstract public static function fromArray(array $data): static;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
