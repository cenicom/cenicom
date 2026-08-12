<?php

declare(strict_types=1);

namespace App\Core\Audit\Listeners;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class AuditAuthorizationChangedListener
{
    public function __construct(
        private AuditRecorderInterface $recorder,
        private IdentityInterface $identity,
    ) {}

    public function handle(
        AuthorizationChanged $event
    ): void {
        $subject = match ($event->scope) {
            AuthorizationChanged::SCOPE_USER
            => new AuditSubject(
                type: 'user',
                id: $event->identityId,
            ),

            AuthorizationChanged::SCOPE_ROLE
            => new AuditSubject(
                type: 'role',
                id: $event->role,
            ),

            default => null,
        };

        $this->recorder->record(
            new AuditEntry(
                actor: new AuditActor(
                    id: $this->identity->id(),
                    name: $this->identity->name(),
                    authenticated: $this->identity->authenticated(),
                ),
                action: 'authorization.changed',
                subject: $subject,
                metadata: [
                    'scope' => $event->scope,
                ],
                result: 'success',
                occurredAt: new \DateTimeImmutable(),
            )
        );
    }
}
