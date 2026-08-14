<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class FiltersComponentTest extends TestCase
{
    public function test_renders_filters_container(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters />'
        );

        $view->assertSee(
            'cn-crud__filters',
            false
        );
    }

    public function test_renders_filter_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters title="Filtrar instituciones" />'
        );

        $view->assertSee(
            'Filtrar instituciones'
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters>
                <input type="text" name="search">
            </x-cn.crud.filters>'
        );

        $view->assertSee(
            'name="search"',
            false
        );
    }

    public function test_does_not_render_title_when_not_provided(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters />'
        );

        $view->assertDontSee(
            'cn-crud__filters-title',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters
                id="institution-filters"
            />'
        );

        $view->assertSee(
            'id="institution-filters"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.crud.filters
                class="custom-filters"
            />'
        );

        $view->assertSee(
            'cn-crud__filters',
            false
        );

        $view->assertSee(
            'custom-filters',
            false
        );
    }
}
