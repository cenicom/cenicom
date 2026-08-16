<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions\Bootstrap;

use App\Core\Security\Permissions\Bootstrap\PermissionDefinitionBootstrapper;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionBootstrapperTest extends TestCase
{
    public function test_boot_executes_valid_definition_with_injected_registrar(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $definition = new class implements PermissionDefinitionInterface {
            public static ?PermissionRegistrarInterface $receivedRegistrar = null;

            public function register(
                PermissionRegistrarInterface $permissions
            ): void {
                self::$receivedRegistrar = $permissions;
            }
        };

        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $definition::class,
            ]);

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $registry,
            $registrar,
        );

        $bootstrapper->boot();

        self::assertSame(
            $registrar,
            $definition::$receivedRegistrar
        );
    }

    public function test_boot_executes_valid_definition(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $definition = new class implements PermissionDefinitionInterface {
            public static bool $registered = false;

            public function register(
                PermissionRegistrarInterface $permissions
            ): void {
                self::$registered = true;
            }
        };

        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $definition::class,
            ]);

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $registry,
            $registrar,
        );

        $bootstrapper->boot();

        self::assertTrue(
            $definition::$registered
        );
    }

    public function test_boot_executes_multiple_valid_definitions(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $first = new class implements PermissionDefinitionInterface {
            public static int $calls = 0;

            public function register(
                PermissionRegistrarInterface $permissions
            ): void {
                self::$calls++;
            }
        };

        $second = new class implements PermissionDefinitionInterface {
            public static int $calls = 0;

            public function register(
                PermissionRegistrarInterface $permissions
            ): void {
                self::$calls++;
            }
        };

        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $first::class,
                $second::class,
            ]);

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $registry,
            $registrar,
        );

        $bootstrapper->boot();

        self::assertSame(1, $first::$calls);
        self::assertSame(1, $second::$calls);
    }

    public function test_boot_ignores_invalid_definition(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                \stdClass::class,
            ]);

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $registry,
            $registrar,
        );

        $bootstrapper->boot();

        self::assertTrue(true);
    }

    public function test_boot_does_nothing_when_registry_is_empty(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([]);

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $registry,
            $registrar,
        );

        $bootstrapper->boot();

        self::assertTrue(true);
    }
}
