<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\Contracts\RoleCheckerInterface;
use App\Core\Security\Roles\RoleChecker;
use Tests\TestCase;

final class RoleCheckerBindingTest extends TestCase
{
    public function test_checker_implements_contract(): void
    {
        $checker = new RoleChecker();

        $this->assertInstanceOf(
            RoleCheckerInterface::class,
            $checker
        );
    }

    public function test_container_resolves_checker_interface(): void
    {
        $checker = $this->app->make(
            RoleCheckerInterface::class
        );

        $this->assertNotNull(
            $checker
        );
    }

    public function test_resolved_instance_is_role_checker(): void
    {
        $checker = $this->app->make(
            RoleCheckerInterface::class
        );

        $this->assertInstanceOf(
            RoleChecker::class,
            $checker
        );
    }

    public function test_checker_is_singleton(): void
    {
        $first = $this->app->make(
            RoleCheckerInterface::class
        );

        $second = $this->app->make(
            RoleCheckerInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
