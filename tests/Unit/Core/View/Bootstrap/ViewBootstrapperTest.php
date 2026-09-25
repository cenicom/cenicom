<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Bootstrap;

use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Contracts\ViewPathResolverInterface;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\Registry\ViewDefinitionRegistry;
use App\Core\View\ViewRegistry;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Tests\TestCase;

final class ViewBootstrapperTest extends TestCase
{
    public function test_executes_registered_view_definitions(): void
    {
        $definitionRegistry = new ViewDefinitionRegistry();

        $definitionRegistry->add(
            \App\Modules\Institution\View\InstitutionView::class
        );

        $registry = new ViewRegistry();

        $finder = new FileViewFinder(
            new Filesystem(),
            [],
            ['blade.php', 'php'],
        );

        $resolver = $this->createMock(
            ViewPathResolverInterface::class,
        );

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(
                'app/Modules/Institution/Resources/Views',
            )
            ->willReturn(
                base_path('app/Modules/Institution/Resources/Views'),
            );

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
            $resolver,
        );

        $bootstrapper = new ViewBootstrapper(
            $definitionRegistry,
            $registrar,
        );

        $bootstrapper->boot();

        $this->assertSame(
            'app/Modules/Institution/Resources/Views',
            $registry->path('institutions'),
        );
    }

    public function test_ignores_invalid_definitions(): void
    {
        $definitionRegistry = new ViewDefinitionRegistry();

        $definitionRegistry->add(
            get_class(new class {})
        );

        $registry = new ViewRegistry();

        $finder = new FileViewFinder(
            new Filesystem(),
            [],
            ['blade.php', 'php'],
        );

        $resolver = $this->createMock(
            ViewPathResolverInterface::class,
        );

        $resolver
            ->expects($this->never())
            ->method('resolve');

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
            $resolver,
        );

        $bootstrapper = new ViewBootstrapper(
            $definitionRegistry,
            $registrar,
        );

        $bootstrapper->boot();

        $this->assertSame(
            [],
            $registry->all(),
        );
    }
}
