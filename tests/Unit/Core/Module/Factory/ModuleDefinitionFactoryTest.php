<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Factory;

use App\Core\Module\Factory\ModuleDefinitionFactory;
use Tests\Fixtures\Providers\BlogServiceProvider;
use Tests\Fixtures\Providers\UsersServiceProvider;
use Tests\TestCase;

final class ModuleDefinitionFactoryTest extends TestCase
{
    private ModuleDefinitionFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ModuleDefinitionFactory();
    }

    public function test_throws_exception_when_name_is_missing(): void
    {
        // ...
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/MissingName/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest must define the "name" key.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_name_is_empty(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyName/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "name" must be a non-empty string.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_name_is_not_string(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidName/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "name" must be a non-empty string.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_namespace_is_missing(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/MissingNamespace/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest must define the "namespace" key.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_namespace_is_empty(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyNamespace/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "namespace" must be a non-empty string.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_namespace_is_not_string(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidNamespace/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "namespace" must be a non-empty string.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_providers_is_missing(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/MissingProviders/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest must define the "providers" key.'
        );

        $this->factory->create($manifest);
    }

    public function test_throws_exception_when_providers_is_not_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidProviders/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "providers" must be an array.'
        );

        $this->factory->create($manifest);
    }

    public function test_creates_definition_with_empty_providers(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyProviders/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame('Blog', $definition->name);

        $this->assertSame(
            'Tests\\Fixtures\\Modules\\Blog',
            $definition->namespace
        );

        $this->assertSame([], $definition->providers);

        $this->assertSame(
            dirname($manifest),
            $definition->basePath
        );

        $this->assertSame(
            $manifest,
            $definition->manifestPath
        );
    }

    public function test_creates_definition_with_multiple_providers(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/MultipleProviders/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame('Blog', $definition->name);

        $this->assertSame(
            'Tests\\Fixtures\\Modules\\Blog',
            $definition->namespace
        );

        $this->assertCount(2, $definition->providers);

        $this->assertSame(
            [
                BlogServiceProvider::class,
                UsersServiceProvider::class,
            ],
            $definition->providers
        );

        $this->assertSame(
            dirname($manifest),
            $definition->basePath
        );

        $this->assertSame(
            $manifest,
            $definition->manifestPath
        );
    }

    public function test_throws_exception_when_manifest_does_not_return_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidReturn/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest must return an array.'
        );

        $this->factory->create($manifest);
    }

    public function test_enabled_defaults_to_true_when_missing(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyProviders/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertTrue($definition->enabled);
    }

    public function test_creates_definition_with_enabled_true(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EnabledTrue/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertTrue($definition->enabled);
    }

    public function test_creates_definition_with_enabled_false(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EnabledFalse/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertFalse($definition->enabled);
    }

    public function test_throws_exception_when_enabled_is_not_boolean(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidEnabled/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "enabled" must be a boolean.'
        );

        $this->factory->create($manifest);
    }

    public function test_creates_definition_with_permission_definitions(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/PermissionDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [
                \App\Modules\Institution\Security\InstitutionPermissionDefinition::class,
                \App\Modules\Inventory\Security\InventoryPermissionDefinition::class,
            ],
            $definition->permissionDefinitions
        );
    }

    public function test_permission_definitions_default_to_empty_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyProviders/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [],
            $definition->permissionDefinitions
        );
    }

    public function test_throws_exception_when_permission_definitions_is_not_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidPermissionDefinitions/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "permission_definitions" must be an array.'
        );

        $this->factory->create($manifest);
    }

    public function test_creates_definition_with_navigation_definitions(): void
    {
        $definition = $this->factory->create(
            base_path('tests/Fixtures/ModuleDefinitionFactory/NavigationDefinitions/module.php')
        );

        self::assertSame(
            [
                \App\Modules\Institution\Navigation\InstitutionNavigation::class,
            ],
            $definition->navigationDefinitions
        );
    }

    public function test_navigation_definitions_default_to_empty_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyNavigationDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [],
            $definition->navigationDefinitions
        );
    }

    public function test_throws_exception_when_navigation_definitions_is_not_array(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "navigation_definitions" must be an array.'
        );

        $this->factory->create(
            base_path(
                'tests/Fixtures/ModuleDefinitionFactory/InvalidNavigationDefinitions/module.php'
            )
        );
    }

    public function test_creates_definition_with_crud_definitions(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/CrudDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [
                \App\Core\Crud\Contracts\CrudDefinitionInterface::class,
            ],
            $definition->crudDefinitions
        );
    }

    public function test_crud_definitions_default_to_empty_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyCrudDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [],
            $definition->crudDefinitions
        );
    }

    public function test_throws_exception_when_crud_definitions_is_not_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidCrudDefinitions/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "crud_definitions" must be an array.'
        );

        $this->factory->create($manifest);
    }

    public function test_creates_definition_with_view_definitions(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/ViewDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [
                \App\Modules\Institution\View\InstitutionView::class,
            ],
            $definition->viewDefinitions
        );
    }

    public function test_view_definitions_default_to_empty_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyViewDefinitions/module.php'
        );

        $definition = $this->factory->create($manifest);

        $this->assertSame(
            [],
            $definition->viewDefinitions
        );
    }

    public function test_throws_exception_when_view_definitions_is_not_array(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidViewDefinitions/module.php'
        );

        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest "view_definitions" must be an array.'
        );

        $this->factory->create($manifest);
    }


}
