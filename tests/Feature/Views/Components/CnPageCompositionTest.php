<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class CnPageCompositionTest extends TestCase
{
    public function test_page_can_compose_page_container_breadcrumb_and_header(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            <<<'BLADE'
            <x-cn.layout.page>
                <x-cn.navigation.breadcrumb :items="$items" />

                <x-cn.layout.page-header
                    title="Dashboard"
                    subtitle="Resumen general"
                    icon="dashboard"
                />

                <section>
                    Contenido principal
                </section>
            </x-cn.layout.page>
            BLADE,
            ['items' => $items]
        );

        $view->assertSee('class="cn-page"', false);
        $view->assertSee('class="cn-breadcrumb"', false);
        $view->assertSee('Dashboard');
        $view->assertSee('Resumen general');
        $view->assertSee('dashboard');
        $view->assertSee('Contenido principal');
    }

    public function test_page_composition_preserves_functional_order(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            <<<'BLADE'
            <x-cn.layout.page>
                <x-cn.navigation.breadcrumb :items="$items" />

                <x-cn.layout.page-header title="Dashboard" />

                <section>
                    Contenido principal
                </section>
            </x-cn.layout.page>
            BLADE,
            ['items' => $items]
        );

        $view->assertSeeInOrder([
            'class="cn-breadcrumb"',
            'Dashboard',
            'Contenido principal',
        ], false);
    }
}
