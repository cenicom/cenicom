<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class LabelComponentTest extends TestCase
{
    public function test_renders_label_element(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label>Nombre</x-cn.forms.label>'
        );

        $view->assertSee('<label', false);
        $view->assertSee('Nombre');
    }

    public function test_renders_label_class(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label>Nombre</x-cn.forms.label>'
        );

        $view->assertSee(
            'cn-label',
            false
        );
    }

    public function test_supports_for_attribute(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label for="name">Nombre</x-cn.forms.label>'
        );

        $view->assertSee(
            'for="name"',
            false
        );
    }

    public function test_renders_label_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label for="name">
                Nombre completo
            </x-cn.forms.label>'
        );

        $view->assertSee('Nombre completo');
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label
                for="name"
                data-test="label"
                aria-label="Nombre"
            >
                Nombre
            </x-cn.forms.label>'
        );

        $view->assertSee(
            'data-test="label"',
            false
        );

        $view->assertSee(
            'aria-label="Nombre"',
            false
        );
    }

    public function test_supports_required_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.label
                for="name"
                :required="true"
            >
                Nombre
            </x-cn.forms.label>'
        );

        $view->assertSee(
            'cn-label-required',
            false
        );
    }
}
