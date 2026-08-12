<?php

declare(strict_types=1);

namespace App\Core\Audit\DTO;

final readonly class AuditActor
{
    public function __construct(
        public int|string|null $id,
        public string $name,
        public bool $authenticated,
    ) {
    }
}
