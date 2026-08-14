<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;


use Tests\TestCase;

final class FormsComponentsContractTest extends TestCase
{
    public function test_forms_components_can_be_composed_together(): void
    {
        $view = $this
            ->blade(
                <<<'BLADE'
                <x-cn.forms.form
                    action="/institutions"
                    method="POST"
                    data-testid="institution-form"
                >
                    <x-cn.forms.group class="cn-group-2">

                        <x-cn.forms.field id="institution-name-field">

                            <x-cn.forms.label
                                for="institution-name"
                                required
                            >
                                Nombre
                            </x-cn.forms.label>

                            <x-cn.forms.input
                                id="institution-name"
                                name="institution_name"
                                type="text"
                            />

                            <x-cn.forms.help id="institution-name-help">
                                Nombre de la institución.
                            </x-cn.forms.help>

                            <x-cn.forms.hint id="institution-name-hint">
                                Ingrese el nombre oficial.
                            </x-cn.forms.hint>

                        </x-cn.forms.field>

                        <x-cn.forms.field id="institution-code-field">

                            <x-cn.forms.label
                                for="institution-code"
                            >
                                Código
                            </x-cn.forms.label>

                            <x-cn.forms.input
                                id="institution-code"
                                name="institution_code"
                                type="text"
                            />

                            <x-cn.forms.display
                                id="institution-code-display"
                            >
                                Código generado
                            </x-cn.forms.display>

                        </x-cn.forms.field>

                    </x-cn.forms.group>
                </x-cn.forms.form>
                BLADE
            );

        $view->assertSee('cn-form', false);
        $view->assertSee('cn-group', false);
        $view->assertSee('cn-group-2', false);
        $view->assertSee('cn-field', false);
        $view->assertSee('cn-label', false);
        $view->assertSee('cn-input', false);
        $view->assertSee('cn-help', false);
        $view->assertSee('cn-hint', false);
        $view->assertSee('cn-display', false);

        $view->assertSee('Nombre');
        $view->assertSee('Nombre de la institución.');
        $view->assertSee('Ingrese el nombre oficial.');
        $view->assertSee('Código generado');
    }

    public function test_forms_error_integrates_with_validation_state(): void
    {
        $view = $this->withViewErrors([
            'institution_name' => 'El nombre es obligatorio.',
        ])->blade(
            <<<'BLADE'
            <x-cn.forms.field>
                <x-cn.forms.input
                    id="institution-name"
                    name="institution_name"
                    type="text"
                />

                <x-cn.forms.error
                    for="institution_name"
                />
            </x-cn.forms.field>
            BLADE
        );

        $view->assertSee('is-invalid', false);
        $view->assertSee('aria-invalid="true"', false);
        $view->assertSee('El nombre es obligatorio.');
        $view->assertSee('id="institution_name-error"', false);
    }
}
