<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class EmptyStateComponentTest extends TestCase
{
    public function test_renders_empty_state_container(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state />'
        );

        $view->assertSee(
            'cn-empty-state',
            false
        );
    }

    public function test_renders_title(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state title="Sin registros" />'
        );

        $view->assertSee(
            'cn-empty-state__title',
            false
        );

        $view->assertSee(
            'Sin registros'
        );
    }

    public function test_renders_description(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state
                description="No existen registros disponibles."
            />'
        );

        $view->assertSee(
            'cn-empty-state__description',
            false
        );

        $view->assertSee(
            'No existen registros disponibles.'
        );
    }

    public function test_renders_icon(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state icon="inbox" />'
        );

        $view->assertSee(
            'cn-empty-state__icon',
            false
        );

        $view->assertSee(
            'fa-inbox',
            false
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state>
                No hay instituciones registradas.
            </x-cn.empty-state>'
        );

        $view->assertSee(
            'cn-empty-state__content',
            false
        );

        $view->assertSee(
            'No hay instituciones registradas.'
        );
    }

    public function test_does_not_render_empty_slot_container_without_content(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state title="Sin registros" />'
        );

        $view->assertDontSee(
            'cn-empty-state__content',
            false
        );
    }

    public function test_renders_actions_slot(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.empty-state title="Sin registros">
                <x-slot:actions>
                    <button>Crear registro</button>
                </x-slot:actions>
            </x-cn.empty-state>
            BLADE
        );

        $view->assertSee(
            'cn-empty-state__actions',
            false
        );

        $view->assertSee(
            'Crear registro'
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state
                id="empty-institutions"
                data-testid="empty-state"
            />'
        );

        $view->assertSee(
            'id="empty-institutions"',
            false
        );

        $view->assertSee(
            'data-testid="empty-state"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.empty-state class="custom-empty-state" />'
        );

        $view->assertSee(
            'cn-empty-state',
            false
        );

        $view->assertSee(
            'custom-empty-state',
            false
        );
    }
}
