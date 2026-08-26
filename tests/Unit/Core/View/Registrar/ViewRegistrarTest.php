<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\ViewRegistry;
use Illuminate\Contracts\View\Factory as ViewFactory;
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

        $views = $this->createMock(
            \Illuminate\Contracts\View\Factory::class
        );

        $registrar = new ViewRegistrar(
            $registry,
            $views,
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

        $views = $this->createMock(
            \Illuminate\Contracts\View\Factory::class
        );

        $registrar = new ViewRegistrar(
            $registry,
            $views,
        );

        self::assertInstanceOf(
            \App\Core\View\Contracts\ViewRegistrarInterface::class,
            $registrar,
        );
    }

    public function test_registers_namespace_in_laravel_view_factory(): void
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

        $views = $this->createMock(
            ViewFactory::class
        );

        $views
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institutions',
                'app/Modules/Institution/resources/views',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $views,
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );
    }

    public function test_duplicate_namespace_is_rejected_before_laravel_registration(): void
    {
        $registry = new ViewRegistry();

        $views = $this->createMock(ViewFactory::class);

        $views
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institution',
                'path/one',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $views,
        );

        $registrar->register(
            'institution',
            'path/one',
        );

        $this->expectException(\LogicException::class);

        $registrar->register(
            'institution',
            'path/two',
        );
    }
}
