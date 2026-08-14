<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class IconComponentTest extends TestCase
{
    public function test_renders_icon(): void
    {
        $view = $this->blade(
            '<x-cn.icon name="arrow-left" />'
        );

        $view->assertSee(
            'fa-arrow-left',
            false
        );
    }

    public function test_renders_font_awesome_base_class(): void
    {
        $view = $this->blade(
            '<x-cn.icon name="building" />'
        );

        $view->assertSee(
            'fas',
            false
        );

        $view->assertSee(
            'fa-building',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.icon
                name="building"
                id="institution-icon"
                data-testid="icon"
            />'
        );

        $view->assertSee(
            'id="institution-icon"',
            false
        );

        $view->assertSee(
            'data-testid="icon"',
            false
        );
    }

    public function test_preserves_icon_classes_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.icon
                name="building"
                class="custom-icon"
            />'
        );

        $view->assertSee(
            'fas',
            false
        );

        $view->assertSee(
            'fa-building',
            false
        );

        $view->assertSee(
            'custom-icon',
            false
        );
    }
}
