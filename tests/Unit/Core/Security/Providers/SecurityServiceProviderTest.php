<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Providers;

use App\Core\Security\Permissions\Bootstrap\PermissionDefinitionBootstrapper;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use App\Core\Security\Permissions\PermissionDefinitionRegistry;
use App\Core\Security\Permissions\PermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistry;
use Tests\TestCase;

final class SecurityServiceProviderTest extends TestCase
{
    public function test_permission_registry_contract_resolves_to_expected_implementation(): void
    {
        self::assertInstanceOf(
            PermissionRegistry::class,
            app(PermissionRegistryInterface::class)
        );
    }

    public function test_permission_registrar_contract_resolves_to_expected_implementation(): void
    {
        self::assertInstanceOf(
            PermissionRegistrar::class,
            app(PermissionRegistrarInterface::class)
        );
    }

    public function test_permission_definition_registry_contract_resolves_to_expected_implementation(): void
    {
        self::assertInstanceOf(
            PermissionDefinitionRegistry::class,
            app(PermissionDefinitionRegistryInterface::class)
        );
    }

    public function test_permission_definition_loader_resolves(): void
    {
        self::assertInstanceOf(
            PermissionDefinitionLoader::class,
            app(PermissionDefinitionLoader::class)
        );
    }

    public function test_permission_definition_bootstrapper_resolves(): void
    {
        self::assertInstanceOf(
            PermissionDefinitionBootstrapper::class,
            app(PermissionDefinitionBootstrapper::class)
        );
    }

    public function test_permission_definition_services_are_singletons(): void
    {
        self::assertSame(
            app(PermissionDefinitionRegistryInterface::class),
            app(PermissionDefinitionRegistryInterface::class)
        );

        self::assertSame(
            app(PermissionRegistrarInterface::class),
            app(PermissionRegistrarInterface::class)
        );

        self::assertSame(
            app(PermissionDefinitionLoader::class),
            app(PermissionDefinitionLoader::class)
        );

        self::assertSame(
            app(PermissionDefinitionBootstrapper::class),
            app(PermissionDefinitionBootstrapper::class)
        );
    }
}
