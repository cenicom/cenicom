<?php

declare(strict_types=1);

namespace App\Core\Audit\DTO;

final readonly class AuditSubject
{
    public function __construct(
        public string $type,
        public int|string|null $id,
    ) {
    }
}
