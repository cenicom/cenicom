<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Bootstrap;

use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Contracts\ViewRegistryInterface;
//use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\Registry\ViewDefinitionRegistry;
use Tests\TestCase;

final class ViewBootstrapperTest extends TestCase
{
    public function test_executes_registered_view_definitions(): void
    {
        // Arrange

        $definitionRegistry = new ViewDefinitionRegistry();

        $definitionRegistry->add(
            \App\Modules\Institution\View\InstitutionView::class
        );

        $registrar = app(
            \App\Core\View\Contracts\ViewRegistrarInterface::class
        );

        $bootstrapper = new ViewBootstrapper(
            $definitionRegistry,
            $registrar,
        );

        // Act

        $bootstrapper->boot();

        // Assert

        $registry = app(
            ViewRegistryInterface::class
        );

        $this->assertSame(
            'app/Modules/Institution/resources/views',
            $registry->path('institution'),
        );
    }

    public function test_ignores_invalid_definitions(): void
    {
        // Arrange

        $definitionRegistry = new ViewDefinitionRegistry();

        $definitionRegistry->add(
            get_class(new class {})
        );

        $registrar = app(
            \App\Core\View\Contracts\ViewRegistrarInterface::class
        );

        $bootstrapper = new ViewBootstrapper(
            $definitionRegistry,
            $registrar,
        );

        // Act

        $bootstrapper->boot();

        // Assert

        $registry = app(
            ViewRegistryInterface::class
        );

        $this->assertSame([], $registry->all());
    }
}
