<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Providers;


use App\Core\Module\Providers\ModuleServiceProvider;
use Tests\TestCase;

final class ModuleServiceProviderTest extends TestCase
{
    public function test_provider_boots_without_exception(): void
    {
        $provider = new ModuleServiceProvider(
            $this->app
        );

        $provider->boot();

        $this->assertTrue(true);
    }

    //🚢 Próxima maniobra autorizada
    //⚓ ERP-INT-004.4.2 — Certificar delegación del Provider hacia ModuleBootstrap
    //Objetivo:
    //Verificar que boot() realmente dispara:
    /*
    public function test_provider_boots_module_system(): void
    {
        $bootstrap = $this->createMock(
            \App\Core\Contracts\Module\ModuleBootstrapInterface::class
        );

        $bootstrap
            ->expects($this->once())
            ->method('bootstrap');

        $this->app->instance(
            \App\Core\Contracts\Module\ModuleBootstrapInterface::class,
            $bootstrap
        );

        $provider = new ModuleServiceProvider(
            $this->app
        );

        $provider->boot();

        $this->assertTrue(true);
    }
        */
}
