<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\ViewRegistry;
use Illuminate\View\ViewFinderInterface;
use PHPUnit\Framework\TestCase;

final class ViewRegistrarTest extends TestCase
{
    public function test_delegates_registration_to_registry(): void
    {
        $registry = $this->createMock(ViewRegistryInterface::class);

        $registry
            ->expects($this->once())
            ->method('register')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $finder = $this->createMock(ViewFinderInterface::class);

        $finder
            ->expects($this->once())
            ->method('addLocation')
            ->with(
                'app/Modules/Institution/Resources/Views',
            );

        $finder
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/Resources/Views',
        );
    }

    public function test_implements_contract(): void
    {
        $registry = $this->createMock(ViewRegistryInterface::class);
        $finder = $this->createMock(ViewFinderInterface::class);

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
        );

        self::assertInstanceOf(
            ViewRegistrarInterface::class,
            $registrar,
        );
    }

    public function test_registers_namespace_in_laravel_view_finder(): void
    {
        $registry = $this->createMock(ViewRegistryInterface::class);

        $registry
            ->expects($this->once())
            ->method('register')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $finder = $this->createMock(ViewFinderInterface::class);

        $finder
            ->expects($this->once())
            ->method('addLocation')
            ->with(
                'app/Modules/Institution/Resources/Views',
            );

        $finder
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/Resources/Views',
        );
    }

    public function test_duplicate_namespace_is_rejected_before_laravel_registration(): void
    {
        $registry = new ViewRegistry();

        $finder = $this->createMock(ViewFinderInterface::class);

        $finder
            ->expects($this->once())
            ->method('addLocation')
            ->with('path/one');

        $finder
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institution',
                'path/one',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
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

    public function test_registers_view_path_as_normal_laravel_location(): void
    {
        $registry = $this->createMock(ViewRegistryInterface::class);

        $registry
            ->expects($this->once())
            ->method('register')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $finder = $this->createMock(ViewFinderInterface::class);

        $finder
            ->expects($this->once())
            ->method('addLocation')
            ->with(
                'app/Modules/Institution/Resources/Views',
            );

        $finder
            ->expects($this->once())
            ->method('replaceNamespace')
            ->with(
                'institutions',
                'app/Modules/Institution/Resources/Views',
            );

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
        );

        $registrar->register(
            'institutions',
            'app/Modules/Institution/Resources/Views',
        );
    }
}
