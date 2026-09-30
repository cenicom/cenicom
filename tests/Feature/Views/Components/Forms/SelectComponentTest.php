<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class SelectComponentTest extends TestCase
{
    public function test_renders_select_with_options(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                :options="[
                    'CO' => 'Colombia',
                    'EC' => 'Ecuador',
                ]"
            />
            BLADE
        );

        $view->assertSee('<select', false);
        $view->assertSee('name="country"', false);
        $view->assertSee('id="country"', false);

        $view->assertSee('value="CO"', false);
        $view->assertSee('Colombia');
        $view->assertSee('value="EC"', false);
        $view->assertSee('Ecuador');
    }

    public function test_generates_id_when_not_provided(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                :options="['CO' => 'Colombia']"
            />
            BLADE
        );

        $view->assertSee('id="country"', false);
    }

    public function test_supports_selected_value(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                value="EC"
                :options="[
                    'CO' => 'Colombia',
                    'EC' => 'Ecuador',
                ]"
            />
            BLADE
        );

        $view->assertSee('value="EC"', false);
        $view->assertSee('selected', false);
    }

    public function test_supports_placeholder(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                placeholder="Seleccione un país"
                required
                :options="[
                    'CO' => 'Colombia',
                ]"
            />
            BLADE
        );

        $view->assertSee(
            'Seleccione un país'
        );

        $view->assertSee('value=""', false);
        $view->assertSee('disabled', false);
        $view->assertSee('selected', false);
    }

    public function test_supports_required_and_disabled(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                required
                disabled
                :options="[
                    'CO' => 'Colombia',
                ]"
            />
            BLADE
        );

        $view->assertSee('required', false);
        $view->assertSee('disabled', false);
    }

    public function test_supports_multiple_selection(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="countries"
                multiple
                :value="['CO', 'EC']"
                :options="[
                    'CO' => 'Colombia',
                    'EC' => 'Ecuador',
                    'PE' => 'Perú',
                ]"
            />
            BLADE
        );

        $view->assertSee('name="countries[]"', false);
        $view->assertSee('multiple', false);

        $view->assertSee('value="CO"', false);
        $view->assertSee('value="EC"', false);

    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-cn.forms.select
                name="country"
                data-testid="country-select"
                aria-label="País"
                class="custom-select"
                :options="[
                    'CO' => 'Colombia',
                ]"
            />
            BLADE
        );

        $view->assertSee(
            'data-testid="country-select"',
            false
        );

        $view->assertSee(
            'aria-label="País"',
            false
        );

        $view->assertSee(
            'custom-select',
            false
        );

        $view->assertSee(
            'cn-select',
            false
        );
    }
}
