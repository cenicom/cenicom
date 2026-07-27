<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Bootstrap\NavigationBootstrapper;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use Tests\TestCase;

final class NavigationBootstrapperTest extends TestCase
{
    public function test_executes_registered_navigation_definitions(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $definitionRegistry->add(
            \App\Modules\Institution\Navigation\InstitutionNavigation::class
        );


        $registrar = app(
            NavigationRegistrarInterface::class
        );


        $bootstrapper = new NavigationBootstrapper(
            $definitionRegistry,
            $registrar
        );


        // Act

        $bootstrapper->boot();


        // Assert

        $tree = app(
            NavigationRegistryInterface::class
        )->tree();


        $this->assertNotNull($tree);
    }


    public function test_ignores_invalid_definitions(): void
    {
        // Arrange

        $definitionRegistry = new NavigationDefinitionRegistry();

        $definitionRegistry->add(get_class(
        new class {}
    )
);


        $registrar = app(
            NavigationRegistrarInterface::class
        );


        $bootstrapper = new NavigationBootstrapper(
            $definitionRegistry,
            $registrar
        );


        // Act

        $bootstrapper->boot();


        // Assert

        $this->assertTrue(true);
    }
}
