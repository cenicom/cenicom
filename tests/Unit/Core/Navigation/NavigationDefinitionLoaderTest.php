<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Loader\NavigationDefinitionLoader;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use Tests\TestCase;

final class NavigationDefinitionLoaderTest extends TestCase
{
    public function test_loads_navigation_definitions(): void
    {
        // Arrange

        $registry = new NavigationDefinitionRegistry();

        $loader = new NavigationDefinitionLoader(
            $registry
        );


        // Act

        $loader->load();


        // Assert

        $this->assertContains(
            InstitutionNavigation::class,
            $registry->definitions()
        );
    }
}
