<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class GroupComponentTest extends TestCase
{
    public function test_renders_group_container(): void
    {
        $view = $this->blade(
            '<x-cn.forms.group />'
        );

        $view->assertSee(
            'cn-group',
            false
        );
    }

    public function test_renders_group_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.group>Contenido del grupo</x-cn.forms.group>'
        );

        $view->assertSee('Contenido del grupo');
    }

    public function test_supports_group_class(): void
    {
        $view = $this->blade(
            '<x-cn.forms.group class="cn-group-2">
            Contenido
        </x-cn.forms.group>'
        );

        $view->assertSee(
            'cn-group cn-group-2',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.group data-test="group-test">Contenido</x-cn.forms.group>'
        );

        $view->assertSee(
            'data-test="group-test"',
            false
        );
    }

    public function test_renders_group_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.group>
                <span>Campo A</span>
                <span>Campo B</span>
            </x-cn.forms.group>'
        );

        $view->assertSee('Campo A');
        $view->assertSee('Campo B');
    }
}
