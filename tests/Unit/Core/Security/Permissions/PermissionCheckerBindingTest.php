<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;
use App\Core\Security\Permissions\PermissionChecker;
use Tests\TestCase;

final class PermissionCheckerBindingTest extends TestCase
{
    public function test_checker_implements_contract(): void
    {
        $checker = $this->app->make(
            PermissionCheckerInterface::class
        );

        $this->assertInstanceOf(
            PermissionCheckerInterface::class,
            $checker
        );
    }


    public function test_container_resolves_checker_interface(): void
    {
        $checker = $this->app->make(
            PermissionCheckerInterface::class
        );

        $this->assertNotNull(
            $checker
        );
    }


    public function test_resolved_instance_is_permission_checker(): void
    {
        $checker = $this->app->make(
            PermissionCheckerInterface::class
        );

        $this->assertInstanceOf(
            PermissionChecker::class,
            $checker
        );
    }


    public function test_checker_is_singleton(): void
    {
        $first = $this->app->make(
            PermissionCheckerInterface::class
        );

        $second = $this->app->make(
            PermissionCheckerInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
