<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use App\Core\Module\DTO\ModuleDefinition;
use Illuminate\Contracts\Foundation\Application;
use Mockery;
use Tests\Fixtures\Bootstrap\FakeClass;
use Tests\TestCase;

final class ModuleProviderRegistrarRejectInvalidProviderTest extends TestCase
{
    public function test_rejects_non_existing_provider(): void
    {
        $provider = 'App\\Modules\\Test\\MissingProvider';

        $validator = Mockery::mock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with($provider)
            ->andReturn(false);


        $app = Mockery::mock(Application::class);

        $app
            ->shouldNotReceive('register');


        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);


        $this->assertFalse(
            $registrar->isRegistered($provider)
        );
    }

    public function test_rejects_non_service_provider(): void
    {
        $provider = FakeClass::class;

        $validator = Mockery::mock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with($provider)
            ->andReturn(false);

        $app = Mockery::mock(Application::class);

        $app
            ->shouldNotReceive('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);

        $this->assertFalse(
            $registrar->isRegistered($provider)
        );
    }

    public function test_register_definition_processes_invalid_providers(): void
    {
        // Arrange

        $definition = new ModuleDefinition(
            name: 'TestModule',
            namespace: 'Tests\\Fixtures\\Bootstrap',
            basePath: '/tmp/TestModule',
            manifestPath: '/tmp/TestModule/module.php',
            providers: [
                'App\\Modules\\Test\\MissingProvider',
                FakeClass::class,
            ],
            permissionDefinitions: [],
            navigationDefinitions: [],
            enabled: true,
        );

        $validator = Mockery::mock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with('App\\Modules\\Test\\MissingProvider')
            ->andReturn(false);

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with(FakeClass::class)
            ->andReturn(false);

        $app = Mockery::mock(Application::class);

        $app
            ->shouldNotReceive('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        // Act

        $registrar->registerDefinition($definition);

        // Assert

        $this->assertFalse(
            $registrar->isRegistered('App\\Modules\\Test\\MissingProvider')
        );

        $this->assertFalse(
            $registrar->isRegistered(FakeClass::class)
        );
    }
}
