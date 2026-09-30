<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class SearchComponentTest extends TestCase
{
    public function test_renders_search_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.search
                name="query"
            />'
        );

        $view->assertSee(
            '<input',
            false
        );

        $view->assertSee(
            'type="search"',
            false
        );

        $view->assertSee(
            'name="query"',
            false
        );

        $view->assertSee(
            'id="query"',
            false
        );
    }

    public function test_supports_value_and_placeholder(): void
    {
        $view = $this->blade(
            '<x-cn.forms.search
                name="query"
                value="Institución"
                placeholder="Buscar institución"
            />'
        );

        $view->assertSee(
            'value="Institución"',
            false
        );

        $view->assertSee(
            'placeholder="Buscar institución"',
            false
        );
    }

    public function test_renders_search_specific_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.search
                name="query"
            />'
        );

        $view->assertSee(
            'autocomplete="off"',
            false
        );

        $view->assertSee(
            'spellcheck="false"',
            false
        );
    }

    public function test_supports_required_readonly_disabled_and_autofocus(): void
    {
        $view = $this->blade(
            '<x-cn.forms.search
                name="query"
                required
                readonly
                disabled
                autofocus
            />'
        );

        $view->assertSee(
            'required',
            false
        );

        $view->assertSee(
            'readonly',
            false
        );

        $view->assertSee(
            'disabled',
            false
        );

        $view->assertSee(
            'autofocus',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.search
                name="query"
                data-testid="search-input"
                aria-label="Buscar"
                class="custom-search"
            />'
        );

        $view->assertSee(
            'data-testid="search-input"',
            false
        );

        $view->assertSee(
            'aria-label="Buscar"',
            false
        );

        $view->assertSee(
            'custom-search',
            false
        );

        $view->assertSee(
            'cn-input',
            false
        );
    }
}
