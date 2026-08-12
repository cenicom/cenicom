<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class AuditActorTest extends TestCase
{
    public function test_creates_authenticated_actor(): void
    {
        $actor = new \App\Core\Audit\DTO\AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $this->assertSame(15, $actor->id);
        $this->assertSame('Juan Pérez', $actor->name);
        $this->assertTrue($actor->authenticated);
    }

    public function test_creates_guest_actor(): void
    {
        $actor = new \App\Core\Audit\DTO\AuditActor(
            id: null,
            name: 'Guest',
            authenticated: false,
        );

        $this->assertNull($actor->id);
        $this->assertSame('Guest', $actor->name);
        $this->assertFalse($actor->authenticated);
    }

    public function test_preserves_string_identifier(): void
    {
        $actor = new \App\Core\Audit\DTO\AuditActor(
            id: 'user-123',
            name: 'Juan Pérez',
            authenticated: true,
        );

        $this->assertSame('user-123', $actor->id);
    }

    public function test_exposes_authentication_state(): void
    {
        $authenticated = new \App\Core\Audit\DTO\AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $guest = new \App\Core\Audit\DTO\AuditActor(
            id: null,
            name: 'Guest',
            authenticated: false,
        );

        $this->assertTrue($authenticated->authenticated);
        $this->assertFalse($guest->authenticated);
    }
}
