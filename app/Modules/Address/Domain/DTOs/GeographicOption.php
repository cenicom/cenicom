<?php

declare(strict_types=1);

namespace App\Modules\Address\Domain\DTOs;

final readonly class GeographicOption
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}
