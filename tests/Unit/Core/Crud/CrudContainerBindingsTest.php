<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\Contracts\ResourceRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use App\Core\Crud\ResourceService;
use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\CrudPermissionResolver;
use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Crud\CrudPermissionService;
use App\Core\Crud\Contracts\CrudPermissionRegistrarInterface;
use App\Core\Crud\CrudPermissionRegistrar;
use Tests\TestCase;

final class CrudContainerBindingsTest extends TestCase
{
    public function test_crud_registrar_interface_resolves_to_crud_registrar(): void
    {
        self::assertInstanceOf(
            CrudRegistrar::class,
            $this->app->make(CrudRegistrarInterface::class)
        );
    }

    public function test_resource_registry_interface_resolves_to_crud_registrar(): void
    {
        self::assertInstanceOf(
            CrudRegistrar::class,
            $this->app->make(ResourceRegistryInterface::class)
        );
    }

    public function test_crud_registrar_and_resource_registry_share_same_instance(): void
    {
        self::assertSame(
            $this->app->make(CrudRegistrarInterface::class),
            $this->app->make(ResourceRegistryInterface::class)
        );
    }

    public function test_crud_definition_registry_interface_resolves_to_registry(): void
    {
        self::assertInstanceOf(
            CrudDefinitionRegistry::class,
            $this->app->make(CrudDefinitionRegistryInterface::class)
        );
    }

    public function test_resource_service_interface_resolves_to_resource_service(): void
    {
        self::assertInstanceOf(
            ResourceService::class,
            $this->app->make(ResourceServiceInterface::class)
        );
    }

    public function test_crud_permission_resolver_interface_resolves_to_crud_permission_resolver(): void
    {
        self::assertInstanceOf(
            CrudPermissionResolver::class,
            $this->app->make(CrudPermissionResolverInterface::class)
        );
    }

    public function test_crud_permission_service_interface_resolves_to_crud_permission_service(): void
    {
        self::assertInstanceOf(
            CrudPermissionService::class,
            $this->app->make(CrudPermissionServiceInterface::class)
        );
    }

    public function test_crud_permission_registrar_interface_resolves_to_crud_permission_registrar(): void
    {
        self::assertInstanceOf(
            CrudPermissionRegistrar::class,
            $this->app->make(
                CrudPermissionRegistrarInterface::class
            )
        );
    }
}
