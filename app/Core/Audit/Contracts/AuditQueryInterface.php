<?php

declare(strict_types=1);

namespace App\Core\Audit\Contracts;

use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\DTO\AuditSubject;
use DateTimeImmutable;

interface AuditQueryInterface
{
    /**
     * Consulta las entradas de auditoría asociadas
     * a un sujeto determinado.
     */
    public function bySubject(AuditSubject $subject): iterable;

    /** * Consulta las entradas de auditoría por acción. */
    public function byAction(string $action): iterable;

    /** * Consulta las entradas de auditoría asociadas * a un actor determinado. */
    public function byActor(AuditActor $actor): iterable;

    public function between(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): iterable;

    public function find(int $id): ?AuditEntryData;
}
