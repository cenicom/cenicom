<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\Services\NavigationService;
use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Authorization\NavigationPermissionResolver;
use Tests\TestCase;

final class NavigationContainerResolutionTest extends TestCase
{
    public function test_navigation_dependencies_are_resolvable(): void
    {
        $builder = app(
            NavigationBuilderInterface::class
        );

        $service = app(
            NavigationServiceInterface::class
        );

        $resolver = app(
            NavigationPermissionResolverInterface::class
        );


        $this->assertInstanceOf(
            NavigationBuilder::class,
            $builder
        );


        $this->assertInstanceOf(
            NavigationService::class,
            $service
        );


        $this->assertInstanceOf(
            NavigationPermissionResolver::class,
            $resolver
        );
    }
}
