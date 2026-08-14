<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class BadgeComponentTest extends TestCase
{
    public function test_renders_badge_container(): void
    {
        $view = $this->blade(
            '<x-cn.badge>
                Estado
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge',
            false
        );
    }

    public function test_renders_badge_content(): void
    {
        $view = $this->blade(
            '<x-cn.badge>
                Activo
            </x-cn.badge>'
        );

        $view->assertSee(
            'Activo'
        );
    }

    public function test_uses_neutral_variant_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.badge>
                Estado
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge--neutral',
            false
        );
    }

    public function test_uses_medium_size_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.badge>
                Estado
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge--md',
            false
        );
    }

    public function test_supports_custom_variant(): void
    {
        $view = $this->blade(
            '<x-cn.badge variant="success">
                Activo
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge--success',
            false
        );
    }

    public function test_supports_custom_size(): void
    {
        $view = $this->blade(
            '<x-cn.badge size="sm">
                Activo
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge--sm',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.badge
                id="status-badge"
                data-testid="badge"
            >
                Activo
            </x-cn.badge>'
        );

        $view->assertSee(
            'id="status-badge"',
            false
        );

        $view->assertSee(
            'data-testid="badge"',
            false
        );
    }

    public function test_preserves_badge_classes_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.badge class="custom-badge">
                Activo
            </x-cn.badge>'
        );

        $view->assertSee(
            'cn-badge',
            false
        );

        $view->assertSee(
            'cn-badge--neutral',
            false
        );

        $view->assertSee(
            'cn-badge--md',
            false
        );

        $view->assertSee(
            'custom-badge',
            false
        );
    }
}
