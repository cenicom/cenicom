<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class AlertsComponentTest extends TestCase
{
    public function test_renders_alert_container(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="success">
                Operación realizada.
            </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert',
            false
        );
    }

    public function test_renders_alert_content(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="success">
                Operación realizada.
            </x-cn.alert>'
        );

        $view->assertSee(
            'Operación realizada.'
        );
    }

    public function test_renders_success_alert_type(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="success">
                Guardado correctamente.
            </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert-success',
            false
        );
    }

    public function test_renders_warning_alert_type(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="warning">
                Revise los datos.
            </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert-warning',
            false
        );
    }

    public function test_renders_danger_alert_type(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="danger">
                No fue posible completar la operación.
            </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert-danger',
            false
        );
    }

    public function test_renders_info_alert_type(): void
    {
        $view = $this->blade(
            '<x-cn.alert type="info">
                Información adicional.
            </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert-info',
            false
        );
    }

    public function test_uses_info_type_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.alert>
            Información.
        </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert-info',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.alert
            id="system-alert"
            data-testid="alert"
        >
            Información.
        </x-cn.alert>'
        );

        $view->assertSee(
            'id="system-alert"',
            false
        );

        $view->assertSee(
            'data-testid="alert"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.alert class="custom-alert">
            Información.
        </x-cn.alert>'
        );

        $view->assertSee(
            'cn-alert',
            false
        );

        $view->assertSee(
            'custom-alert',
            false
        );
    }
}
