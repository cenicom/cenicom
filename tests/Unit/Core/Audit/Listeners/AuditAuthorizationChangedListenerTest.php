<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Audit\Listeners;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\Listeners\AuditAuthorizationChangedListener;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use App\Core\Security\Contracts\IdentityInterface;
use PHPUnit\Framework\TestCase;

final class AuditAuthorizationChangedListenerTest extends TestCase
{


    public function test_records_user_authorization_change_with_current_actor(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(15);

        $identity
            ->method('name')
            ->willReturn('Juan Pérez');

        $identity
            ->method('authenticated')
            ->willReturn(true);

        $recorder = $this->createMock(
            AuditRecorderInterface::class
        );

        $recorder
            ->expects($this->once())
            ->method('record')
            ->with(
                $this->callback(
                    function (AuditEntry $entry) use ($identity): bool {
                        return $entry->action
                            === 'authorization.changed'
                            && $entry->actor->id === $identity->id()
                            && $entry->actor->name === $identity->name()
                            && $entry->actor->authenticated
                            === $identity->authenticated()
                            && $entry->subject?->type === 'user'
                            && $entry->subject?->id === 15;
                    }
                )
            );

        $listener = new AuditAuthorizationChangedListener(
            $recorder,
            $identity,
        );

        $listener->handle(
            AuthorizationChanged::user(15)
        );
    }

    public function test_records_role_authorization_change_with_current_actor(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(15);

        $identity
            ->method('name')
            ->willReturn('Juan Pérez');

        $identity
            ->method('authenticated')
            ->willReturn(true);

        $recorder = $this->createMock(
            AuditRecorderInterface::class
        );

        $recorder
            ->expects($this->once())
            ->method('record')
            ->with(
                $this->callback(
                    function (AuditEntry $entry) use ($identity): bool {
                        return $entry->action
                            === 'authorization.changed'
                            && $entry->actor->id === $identity->id()
                            && $entry->actor->name === $identity->name()
                            && $entry->actor->authenticated
                            === $identity->authenticated()
                            && $entry->subject?->type === 'role'
                            && $entry->subject?->id === 'teacher'
                            && $entry->metadata['scope'] === 'role'
                            && $entry->result === 'success';
                    }
                )
            );

        $listener = new AuditAuthorizationChangedListener(
            $recorder,
            $identity,
        );

        $listener->handle(
            AuthorizationChanged::role('teacher')
        );
    }
}
