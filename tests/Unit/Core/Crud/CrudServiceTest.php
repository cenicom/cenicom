<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudDefinitionInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudService;
use PHPUnit\Framework\TestCase;

final class CrudServiceTest extends TestCase
{
    public function test_boot_executes_valid_definition(): void
    {
        $registrar = $this->createMock(
            CrudRegistrarInterface::class
        );

        $definition = new class implements CrudDefinitionInterface {
            public static bool $registered = false;

            public function register(
                CrudRegistrarInterface $crud
            ): void {
                self::$registered = true;
            }
        };

        $registry = $this->createMock(
            CrudDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $definition::class,
            ]);

        $service = new CrudService(
            $registry,
            $registrar,
        );

        $service->boot();

        self::assertTrue(
            $definition::$registered
        );
    }

    public function test_boot_injects_registered_crud_registrar(): void
    {
        $registrar = $this->createMock(
            CrudRegistrarInterface::class
        );

        $definition = new class implements CrudDefinitionInterface {
            public static ?CrudRegistrarInterface $receivedRegistrar = null;

            public function register(
                CrudRegistrarInterface $crud
            ): void {
                self::$receivedRegistrar = $crud;
            }
        };

        $registry = $this->createMock(
            CrudDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $definition::class,
            ]);

        $service = new CrudService(
            $registry,
            $registrar,
        );

        $service->boot();

        self::assertSame(
            $registrar,
            $definition::$receivedRegistrar
        );
    }

    public function test_boot_executes_multiple_valid_definitions(): void
    {
        $registrar = $this->createMock(
            CrudRegistrarInterface::class
        );

        $first = new class implements CrudDefinitionInterface {
            public static int $calls = 0;

            public function register(
                CrudRegistrarInterface $crud
            ): void {
                self::$calls++;
            }
        };

        $second = new class implements CrudDefinitionInterface {
            public static int $calls = 0;

            public function register(
                CrudRegistrarInterface $crud
            ): void {
                self::$calls++;
            }
        };

        $registry = $this->createMock(
            CrudDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                $first::class,
                $second::class,
            ]);

        $service = new CrudService(
            $registry,
            $registrar,
        );

        $service->boot();

        self::assertSame(1, $first::$calls);
        self::assertSame(1, $second::$calls);
    }

    public function test_boot_ignores_invalid_definition(): void
    {
        $registrar = $this->createMock(
            CrudRegistrarInterface::class
        );

        $registry = $this->createMock(
            CrudDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([
                \stdClass::class,
            ]);

        $service = new CrudService(
            $registry,
            $registrar,
        );

        $service->boot();

        self::assertTrue(true);
    }

    public function test_boot_does_nothing_when_registry_is_empty(): void
    {
        $registrar = $this->createMock(
            CrudRegistrarInterface::class
        );

        $registry = $this->createMock(
            CrudDefinitionRegistryInterface::class
        );

        $registry
            ->expects(self::once())
            ->method('definitions')
            ->willReturn([]);

        $service = new CrudService(
            $registry,
            $registrar,
        );

        $service->boot();

        self::assertTrue(true);
    }
}
