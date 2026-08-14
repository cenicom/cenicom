<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class CardComponentTest extends TestCase
{
    public function test_renders_card_container(): void
    {
        $view = $this->blade(
            '<x-cn.card>
                Contenido
            </x-cn.card>'
        );

        $view->assertSee(
            'cn-card',
            false
        );

        $view->assertSee(
            'Contenido'
        );
    }

    public function test_card_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.card
                id="institution-card"
                data-testid="card"
            >
                Contenido
            </x-cn.card>'
        );

        $view->assertSee(
            'id="institution-card"',
            false
        );

        $view->assertSee(
            'data-testid="card"',
            false
        );
    }

    public function test_card_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.card class="custom-card">
                Contenido
            </x-cn.card>'
        );

        $view->assertSee(
            'cn-card',
            false
        );

        $view->assertSee(
            'custom-card',
            false
        );
    }

    public function test_renders_card_body(): void
    {
        $view = $this->blade(
            '<x-cn.card.body>
                Contenido del cuerpo
            </x-cn.card.body>'
        );

        $view->assertSee(
            'cn-card-body',
            false
        );

        $view->assertSee(
            'Contenido del cuerpo'
        );
    }

    public function test_renders_card_header(): void
    {
        $view = $this->blade(
            '<x-cn.card.header title="Institución">
                Contenido adicional
            </x-cn.card.header>'
        );

        $view->assertSee(
            'cn-card-header',
            false
        );

        $view->assertSee(
            'cn-card-header__title',
            false
        );

        $view->assertSee(
            'Institución'
        );

        $view->assertSee(
            'Contenido adicional'
        );
    }

    public function test_renders_card_footer(): void
    {
        $view = $this->blade(
            '<x-cn.card.footer>
                Acciones
            </x-cn.card.footer>'
        );

        $view->assertSee(
            'cn-card-footer',
            false
        );

        $view->assertSee(
            'Acciones'
        );
    }

    public function test_card_components_can_be_composed_together(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.card>
                <x-cn.card.header title="Institución" />

                <x-cn.card.body>
                    Datos de la institución.
                </x-cn.card.body>

                <x-cn.card.footer>
                    Acciones.
                </x-cn.card.footer>
            </x-cn.card>
            BLADE
        );

        $view->assertSee(
            'cn-card',
            false
        );

        $view->assertSee(
            'cn-card-header',
            false
        );

        $view->assertSee(
            'cn-card-body',
            false
        );

        $view->assertSee(
            'cn-card-footer',
            false
        );

        $view->assertSee(
            'Datos de la institución.'
        );

        $view->assertSee(
            'Acciones.'
        );
    }
}
