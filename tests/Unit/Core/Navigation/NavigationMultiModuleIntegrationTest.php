<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Bootstrap\NavigationBootstrapper;
use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Loader\NavigationDefinitionLoader;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use App\Core\Navigation\Registry\NavigationRegistry;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class NavigationMultiModuleIntegrationTest extends TestCase
{
    public function test_loads_multiple_navigation_definitions(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $loader = new NavigationDefinitionLoader(
            $definitionRegistry
        );


        // Act

        $loader->load();


        // Assert

        $definitions = $definitionRegistry->definitions();

        $this->assertCount(
            2,
            $definitions
        );
    }


    public function test_bootstraps_multiple_module_navigation(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $navigationRegistry = new NavigationRegistry();


        $loader = new NavigationDefinitionLoader(
            $definitionRegistry
        );


        $registrar = new NavigationRegistrar(
            $navigationRegistry
        );


        $bootstrapper = new NavigationBootstrapper(
            $definitionRegistry,
            $registrar
        );


        // Act

        $loader->load();

        $bootstrapper->boot();


        // Assert

        $tree = $navigationRegistry->tree();

        $this->assertNotNull(
            $tree
        );
    }


    public function test_builds_tree_with_multiple_module_groups(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $navigationRegistry = new NavigationRegistry();


        $loader = new NavigationDefinitionLoader(
            $definitionRegistry
        );


        $registrar = new NavigationRegistrar(
            $navigationRegistry
        );


        $bootstrapper = new NavigationBootstrapper(
            $definitionRegistry,
            $registrar
        );


        $permissionResolver = $this->createMock(
            NavigationPermissionResolverInterface::class
        );

        $permissionResolver
            ->method('canView')
            ->willReturn(true);

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $builder = new NavigationBuilder(
            $navigationRegistry,
            $permissionResolver,
        );


        // Act

        $loader->load();

        $bootstrapper->boot();

        $tree = $builder->build($identity);


        // Assert

        $this->assertCount(
            2,
            $tree->nodes()
        );
    }


    public function test_preserves_module_navigation_items(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $navigationRegistry = new NavigationRegistry();


        $loader = new NavigationDefinitionLoader(
            $definitionRegistry
        );


        $registrar = new NavigationRegistrar(
            $navigationRegistry
        );


        $bootstrapper = new NavigationBootstrapper(
            $definitionRegistry,
            $registrar
        );


        $permissionResolver = $this->createMock(
            NavigationPermissionResolverInterface::class
        );

        $permissionResolver
            ->method('canView')
            ->willReturn(true);

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $builder = new NavigationBuilder(
            $navigationRegistry,
            $permissionResolver,
        );


        // Act

        $loader->load();

        $bootstrapper->boot();

       $tree = $builder->build($identity);


        // Assert

        $this->assertSame(
            'Administración',
            $tree->nodes()[0]->label()
        );

        $this->assertSame(
            'Inventario',
            $tree->nodes()[1]->label()
        );
    }
}
