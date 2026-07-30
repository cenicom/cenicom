<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use Illuminate\Contracts\Foundation\Application;
use Mockery;
use Tests\TestCase;

final class ModuleProviderRegistrarRegisterValidProviderTest extends TestCase
{
    public function test_registers_valid_provider(): void
    {
        // Arrange

        $provider = 'Tests\\Fixtures\\Bootstrap\\FakeServiceProvider';

        $validator = Mockery::mock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with($provider)
            ->andReturn(true);

        $app = Mockery::mock(Application::class);

        $app
            ->shouldReceive('register')
            ->once()
            ->with($provider);

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);

        $this->assertTrue(
            $registrar->isRegistered($provider)
        );
    }

    public function test_register_provider_is_idempotent(): void
    {
        // Arrange
        $provider = 'Tests\\Fixtures\\Bootstrap\\FakeServiceProvider';

        $validator = Mockery::mock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->shouldReceive('validate')
            ->once()
            ->with($provider)
            ->andReturn(true);

        $app = Mockery::mock(Application::class);

        $app
            ->shouldReceive('register')
            ->once()
            ->with($provider);

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        // Primera llamada
        $registrar->registerProvider($provider);

        // Segunda llamada (debe salir inmediatamente)
        $registrar->registerProvider($provider);

        // Assert
        $this->assertTrue(
            $registrar->isRegistered($provider)
        );
    }
}
