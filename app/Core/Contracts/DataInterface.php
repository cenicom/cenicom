<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface DataInterface
{
    public static function fromArray(array $data): static;

    public function toArray(): array;
}
