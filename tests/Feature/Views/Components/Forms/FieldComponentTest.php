<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class FieldComponentTest extends TestCase
{
    public function test_renders_field_container(): void
    {
        $view = $this->blade(
            '<x-cn.forms.field>
                Contenido
            </x-cn.forms.field>'
        );

        $view->assertSee(
            'cn-field',
            false
        );
    }

    public function test_renders_field_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.field>
                <label>Nombre</label>
            </x-cn.forms.field>'
        );

        $view->assertSee(
            '<label>Nombre</label>',
            false
        );
    }

    public function test_renders_field_data_attribute(): void
    {
        $view = $this->blade(
            '<x-cn.forms.field>
                Contenido
            </x-cn.forms.field>'
        );

        $view->assertSee(
            'data-cn="field"',
            false
        );
    }

    public function test_supports_field_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.field id="user-name">
                Contenido
            </x-cn.forms.field>'
        );

        $view->assertSee(
            'id="user-name"',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.field
                data-testid="user-field"
                aria-label="Campo de usuario"
            >
                Contenido
            </x-cn.forms.field>'
        );

        $view->assertSee(
            'data-testid="user-field"',
            false
        );

        $view->assertSee(
            'aria-label="Campo de usuario"',
            false
        );
    }
}
