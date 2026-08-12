<?php

declare(strict_types=1);

namespace App\Core\Audit\Contracts;

use App\Core\Audit\DTO\AuditEntry;

interface AuditRecorderInterface
{
    public function record(AuditEntry $entry): void;
}
