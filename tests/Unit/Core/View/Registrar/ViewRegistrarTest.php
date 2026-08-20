<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\Registrar\ViewRegistrar;
use PHPUnit\Framework\TestCase;

final class ViewRegistrarTest extends TestCase
{
    public function test_delegates_registration_to_registry(): void
    {
        $registry = $this->createMock(
            ViewRegistryInterface::class
        );

        $registry
            ->expects($this->once())
            ->method('register')
            ->with(
                'institutions',
                'app/Modules/Institution/resources/views',
            );

        $registrar = new ViewRegistrar(
            $registry
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );
    }

    public function test_implements_contract(): void
    {
        $registry = $this->createMock(
            ViewRegistryInterface::class
        );

        $registrar = new ViewRegistrar(
            $registry
        );

        self::assertInstanceOf(
            \App\Core\View\Contracts\ViewRegistrarInterface::class,
            $registrar,
        );
    }
}
