<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use Tests\TestCase;

final class NavigationDefinitionRegistryTest extends TestCase
{
    public function test_adds_navigation_definition(): void
    {
        // Arrange

        $registry = new NavigationDefinitionRegistry();

        $definition = \App\Modules\Institution\Navigation\InstitutionNavigation::class;


        // Act

        $registry->add($definition);


        // Assert

        $this->assertContains(
            $definition,
            $registry->definitions()
        );
    }


    public function test_returns_registered_definitions(): void
    {
        // Arrange

        $registry = new NavigationDefinitionRegistry();

        $first = \App\Modules\Institution\Navigation\InstitutionNavigation::class;

        $second = \App\Modules\Institution\Navigation\InstitutionNavigation::class;


        // Act

        $registry->add($first);
        $registry->add($second);


        // Assert

        $this->assertCount(
            2,
            $registry->definitions()
        );

        $this->assertContains(
            $first,
            $registry->definitions()
        );
    }


    public function test_clear_removes_definitions(): void
    {
        // Arrange

        $registry = new NavigationDefinitionRegistry();

        $registry->add(
            \App\Modules\Institution\Navigation\InstitutionNavigation::class
        );


        // Act

        $registry->clear();


        // Assert

        $this->assertEmpty(
            $registry->definitions()
        );
    }
}
