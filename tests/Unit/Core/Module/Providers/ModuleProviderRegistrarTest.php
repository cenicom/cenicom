<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Providers;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Providers\ModuleProviderRegistrar;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleProviderRegistrarTest extends TestCase
{
    private Application&MockObject $application;

    private ModuleProviderRegistrar $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->application = $this->createMock(
            Application::class
        );

        $this->registrar = new ModuleProviderRegistrar(
            $this->application
        );
    }

    public function test_registers_every_provider(): void
    {
        $definition = $this->createDefinition([
            'App\\Modules\\Blog\\Providers\\BlogServiceProvider',
            'App\\Modules\\Blog\\Providers\\RouteServiceProvider',
        ]);

        $this->application
            ->expects($this->exactly(2))
            ->method('register');

        $this->registrar->registerDefinition($definition);
    }

    public function test_ignores_empty_provider_list(): void
    {
        $definition = $this->createDefinition([]);

        $this->application
            ->expects($this->never())
            ->method('register');

        $this->registrar->registerDefinition($definition);
    }

    public function test_registers_single_provider(): void
    {
        $definition = $this->createDefinition([
            'App\\Modules\\User\\Providers\\UserServiceProvider',
        ]);

        $this->application
            ->expects($this->once())
            ->method('register')
            ->with(
                'App\\Modules\\User\\Providers\\UserServiceProvider'
            );

        $this->registrar->registerDefinition($definition);
    }

    private function createDefinition(array $providers): ModuleDefinition
    {
        return new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.json',
            providers: $providers,
            enabled: true,
        );
    }
}
