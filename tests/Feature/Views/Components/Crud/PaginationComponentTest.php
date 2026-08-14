<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

final class PaginationComponentTest extends TestCase
{
    public function test_renders_pagination_container_when_paginator_has_pages(): void
    {
        $paginator = new LengthAwarePaginator(
            ['Item 1'],
            2,
            1,
            1,
            ['path' => '/institutions']
        );

        $view = $this->blade(
            '<x-cn.crud.pagination :paginator="$paginator" />',
            compact('paginator')
        );

        $view->assertSee(
            'cn-pagination',
            false
        );
    }

    public function test_renders_pagination_links(): void
    {
        $paginator = new LengthAwarePaginator(
            ['Item 1'],
            2,
            1,
            1,
            ['path' => '/institutions']
        );

        $view = $this->blade(
            '<x-cn.crud.pagination :paginator="$paginator" />',
            compact('paginator')
        );

        $view->assertSee(
            'institutions?page=2',
            false
        );
    }

    public function test_does_not_render_when_paginator_has_no_pages(): void
    {
        $paginator = new LengthAwarePaginator(
            ['Item 1'],
            1,
            1,
            1,
            ['path' => '/institutions']
        );

        $view = $this->blade(
            '<x-cn.crud.pagination :paginator="$paginator" />',
            compact('paginator')
        );

        $view->assertDontSee(
            'cn-pagination',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $paginator = new LengthAwarePaginator(
            ['Item 1'],
            2,
            1,
            1,
            ['path' => '/institutions']
        );

        $view = $this->blade(
            '<x-cn.crud.pagination
                :paginator="$paginator"
                id="institution-pagination"
            />',
            compact('paginator')
        );

        $view->assertSee(
            'id="institution-pagination"',
            false
        );
    }

    public function test_preserves_pagination_base_class_with_custom_class(): void
    {
        $paginator = new LengthAwarePaginator(
            ['Item 1'],
            2,
            1,
            1,
            ['path' => '/institutions']
        );

        $view = $this->blade(
            '<x-cn.crud.pagination
                :paginator="$paginator"
                class="custom-pagination"
            />',
            compact('paginator')
        );

        $view->assertSee(
            'cn-pagination',
            false
        );

        $view->assertSee(
            'custom-pagination',
            false
        );
    }
}
