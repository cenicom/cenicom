<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Contracts\ViewAuthorizationInterface;
use App\View\ViewAuthorization;
use PHPUnit\Framework\TestCase;

final class ViewAuthorizationTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $resolver = $this->createMock(
            PermissionResolverInterface::class
        );

        $authorization = new ViewAuthorization(
            $resolver,
        );

        self::assertInstanceOf(
            ViewAuthorizationInterface::class,
            $authorization,
        );
    }

    public function test_delegates_permission_resolution_to_security(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resolver = $this->createMock(
            PermissionResolverInterface::class
        );

        $resolver
            ->expects($this->once())
            ->method('can')
            ->with(
                $identity,
                'institution.create',
            )
            ->willReturn(true);

        $authorization = new ViewAuthorization(
            $resolver,
        );

        self::assertTrue(
            $authorization->can(
                $identity,
                'institution.create',
            ),
        );
    }

    public function test_returns_false_when_security_denies_permission(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resolver = $this->createMock(
            PermissionResolverInterface::class
        );

        $resolver
            ->expects($this->once())
            ->method('can')
            ->with(
                $identity,
                'institution.delete',
            )
            ->willReturn(false);

        $authorization = new ViewAuthorization(
            $resolver,
        );

        self::assertFalse(
            $authorization->can(
                $identity,
                'institution.delete',
            ),
        );
    }
}
