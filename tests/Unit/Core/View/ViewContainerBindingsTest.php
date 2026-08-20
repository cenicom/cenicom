<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\ViewRegistry;
use Tests\TestCase;

final class ViewContainerBindingsTest extends TestCase
{
    public function test_view_registry_interface_resolves_to_view_registry(): void
    {
        $registry = $this->app->make(
            ViewRegistryInterface::class
        );

        self::assertInstanceOf(
            ViewRegistry::class,
            $registry,
        );
    }

    public function test_view_registry_is_singleton(): void
    {
        $first = $this->app->make(
            ViewRegistryInterface::class
        );

        $second = $this->app->make(
            ViewRegistryInterface::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_view_registrar_interface_resolves_to_view_registrar(): void
    {
        $registrar = $this->app->make(
            ViewRegistrarInterface::class
        );

        self::assertInstanceOf(
            ViewRegistrar::class,
            $registrar,
        );
    }

    public function test_view_registrar_is_singleton(): void
    {
        $first = $this->app->make(
            ViewRegistrarInterface::class
        );

        $second = $this->app->make(
            ViewRegistrarInterface::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_view_registrar_uses_registered_view_registry(): void
    {
        $registrar = $this->app->make(
            ViewRegistrarInterface::class
        );

        $registry = $this->app->make(
            ViewRegistryInterface::class
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        self::assertSame(
            'app/Modules/Institution/resources/views',
            $registry->path('institutions'),
        );
    }
}
