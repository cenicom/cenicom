<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use App\Core\Module\DTO\ModuleDefinition;
use Illuminate\Contracts\Foundation\Application;
use RuntimeException;
use Tests\Fixtures\Providers\BlogServiceProvider;
use Tests\Fixtures\Providers\UsersServiceProvider;
use Tests\TestCase;

final class ModuleProviderRegistrarTest extends TestCase
{
    public function test_register_provider_ignores_non_existing_provider(): void
    {
        $provider = 'Fake\\Provider';

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($provider)
            ->willReturn(false);

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->never())
            ->method('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);

        $this->assertFalse(
            $registrar->isRegistered($provider),
        );
    }

    public function test_register_provider_ignores_invalid_provider(): void
    {
        $provider = BlogServiceProvider::class;

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($provider)
            ->willReturn(false);

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->never())
            ->method('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);

        $this->assertFalse(
            $registrar->isRegistered($provider),
        );
    }

    public function test_register_provider_registers_valid_provider(): void
    {
        $provider = BlogServiceProvider::class;

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($provider)
            ->willReturn(true);

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->once())
            ->method('register')
            ->with($provider);

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);

        $this->assertTrue(
            $registrar->isRegistered($provider),
        );
    }

    public function test_register_provider_is_idempotent(): void
    {
        $provider = BlogServiceProvider::class;

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($provider)
            ->willReturn(true);

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->once())
            ->method('register')
            ->with($provider);

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerProvider($provider);
        $registrar->registerProvider($provider);

        $this->assertTrue(
            $registrar->isRegistered($provider),
        );
    }

    public function test_register_definition_registers_all_providers(): void
    {
        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Tests\\Fixtures\\Modules\\Blog',
            basePath: __DIR__,
            manifestPath: __FILE__,
            providers: [
                BlogServiceProvider::class,
                UsersServiceProvider::class,
            ],
            enabled: true,
        );

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(true);

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->exactly(2))
            ->method('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerDefinition($definition);

        foreach ($definition->providers as $provider) {
            $this->assertTrue(
                $registrar->isRegistered($provider),
            );
        }
    }

    public function test_register_definition_with_empty_providers(): void
    {
        $definition = new ModuleDefinition(
            name: 'EmptyModule',
            namespace: 'Tests\\Fixtures\\Modules\\EmptyModule',
            basePath: __DIR__,
            manifestPath: __FILE__,
            providers: [

            ],
            enabled: true,
        );

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class,
        );

        $validator
            ->expects($this->never())
            ->method('validate');

        $app = $this->createMock(Application::class);

        $app
            ->expects($this->never())
            ->method('register');

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app,
        );

        $registrar->registerDefinition($definition);

        $this->assertSame(
            [],
            $definition->providers,
        );
    }

    /*⚓ ERP-INT-004.5.2.2 — MPR-002
        Certificar propagación de excepciones del Container
        Objetivo
        Demostrar que ModuleProviderRegistrar no oculta errores del Container
        de Laravel
        */
    public function test_register_provider_propagates_application_exception(): void
    {
        $provider = BlogServiceProvider::class;

        $validator = $this->createMock(
            ModuleProviderValidatorInterface::class
        );

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($provider)
            ->willReturn(true);

        $app = $this->createMock(
            Application::class
        );

        $app
            ->expects($this->once())
            ->method('register')
            ->with($provider)
            ->willThrowException(
                new \RuntimeException('Container failure.')
            );

        $registrar = new ModuleProviderRegistrar(
            $validator,
            $app
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Container failure.');

        $registrar->registerProvider($provider);
    }
}
