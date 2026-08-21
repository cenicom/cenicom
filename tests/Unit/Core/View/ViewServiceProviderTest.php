<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View;

use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Loader\ViewDefinitionLoader;
use App\Core\View\Registry\ViewDefinitionRegistry;
//use App\Core\View\ViewServiceProvider;
use Tests\TestCase;

final class ViewServiceProviderTest extends TestCase
{
    public function test_view_definition_registry_resolves(): void
    {
        $registry = $this->app->make(
            ViewDefinitionRegistry::class
        );

        self::assertInstanceOf(
            ViewDefinitionRegistry::class,
            $registry,
        );
    }

    public function test_view_definition_registry_is_singleton(): void
    {
        $first = $this->app->make(
            ViewDefinitionRegistry::class
        );

        $second = $this->app->make(
            ViewDefinitionRegistry::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_view_definition_loader_resolves(): void
    {
        $loader = $this->app->make(
            ViewDefinitionLoader::class
        );

        self::assertInstanceOf(
            ViewDefinitionLoader::class,
            $loader,
        );
    }

    public function test_view_definition_loader_is_singleton(): void
    {
        $first = $this->app->make(
            ViewDefinitionLoader::class
        );

        $second = $this->app->make(
            ViewDefinitionLoader::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_view_bootstrapper_resolves(): void
    {
        $bootstrapper = $this->app->make(
            ViewBootstrapper::class
        );

        self::assertInstanceOf(
            ViewBootstrapper::class,
            $bootstrapper,
        );
    }

    public function test_view_bootstrapper_is_singleton(): void
    {
        $first = $this->app->make(
            ViewBootstrapper::class
        );

        $second = $this->app->make(
            ViewBootstrapper::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }
}
