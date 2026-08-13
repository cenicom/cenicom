<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Bootstrap\PermissionDefinitionBootstrapper;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use App\Core\Security\Permissions\PermissionDefinitionRegistry;
use Tests\TestCase;

final class PermissionDefinitionContainerResolutionTest extends TestCase
{
    public function test_permission_definition_registry_interface_resolves(): void
    {
        $registry = app(
            PermissionDefinitionRegistryInterface::class
        );

        self::assertInstanceOf(
            PermissionDefinitionRegistry::class,
            $registry
        );
    }

    public function test_permission_definition_loader_resolves(): void
    {
        $loader = app(
            PermissionDefinitionLoader::class
        );

        self::assertInstanceOf(
            PermissionDefinitionLoader::class,
            $loader
        );
    }

    public function test_permission_definition_bootstrapper_resolves(): void
    {
        $bootstrapper = app(
            PermissionDefinitionBootstrapper::class
        );

        self::assertInstanceOf(
            PermissionDefinitionBootstrapper::class,
            $bootstrapper
        );
    }
}
