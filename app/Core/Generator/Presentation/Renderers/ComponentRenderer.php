<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Renderers;

use App\Core\Generator\Presentation\DTO\InputPresentation;
use App\Core\Generator\Support\StubManager;
use RuntimeException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Renderiza componentes del CN UI Framework utilizando
 * stubs Blade.
 *
 * Su única responsabilidad consiste en transformar un
 * InputPresentation en código Blade listo para insertarse
 * dentro de las vistas generadas.
 *
 * No conoce ColumnDefinition.
 * No conoce ModuleData.
 * No conoce ViewGenerator.
 * No escribe archivos.
 *
 * Toda la interpolación se delega a StubManager.
 *
 * @package App\Core\Generator\Presentation\Renderers
 * @since 2.0.0
 */
final readonly class ComponentRenderer
{
    /*
    |--------------------------------------------------------------------------
    | Component Stubs
    |--------------------------------------------------------------------------
    */

    private const STUB_INPUT = 'components/form/input.stub';

    private const STUB_TEXTAREA = 'components/form/textarea.stub';

    private const STUB_SELECT = 'components/form/select.stub';

    private const STUB_CHECKBOX = 'components/form/checkbox.stub';

    private const STUB_DATE = 'components/form/date.stub';

    private const STUB_NUMBER = 'components/form/number.stub';

    private const STUB_EMAIL = 'components/form/email.stub';

    private const STUB_PASSWORD = 'components/form/password.stub';

    private const STUB_COLUMN = 'components/table/column.stub';

    private const STUB_FIELD = 'components/show/field.stub';

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        private StubManager $stubManager,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Public API
    |--------------------------------------------------------------------------
    */

    /**
     * Renderiza un componente Blade a partir de un InputPresentation.
     */
    public function render(
        InputPresentation $input,
    ): string {

        return match ($input->component->component) {

            'input' => $this->renderInput($input),

            'textarea' => $this->renderTextarea($input),

            'select' => $this->renderSelect($input),

            'checkbox' => $this->renderCheckbox($input),

            'date' => $this->renderDate($input),

            'number' => $this->renderNumber($input),

            'email' => $this->renderEmail($input),

            'password' => $this->renderPassword($input),

            default => throw new RuntimeException(
                sprintf(
                    'Unsupported component [%s].',
                    $input->component->component,
                ),
            ),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Renderers
    |--------------------------------------------------------------------------
    */

    private function renderInput(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_INPUT,
            $this->variables($input),
        );
    }

    private function renderTextarea(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_TEXTAREA,
            $this->variables($input),
        );
    }

    private function renderSelect(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_SELECT,
            $this->variables($input),
        );
    }

    private function renderCheckbox(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_CHECKBOX,
            $this->variables($input),
        );
    }

    private function renderDate(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_DATE,
            $this->variables($input),
        );
    }

    private function renderNumber(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_NUMBER,
            $this->variables($input),
        );
    }

    private function renderEmail(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_EMAIL,
            $this->variables($input),
        );
    }

    private function renderPassword(
        InputPresentation $input,
    ): string {
        return $this->renderStub(
            self::STUB_PASSWORD,
            $this->variables($input),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Renderiza un stub utilizando StubManager.
     */
    private function renderStub(
        string $stub,
        array $variables,
    ): string {
        return $this->stubManager->render(
            $stub,
            $variables,
        );
    }

    /**
     * Construye el mapa de placeholders.
     *
     * @return array<string,mixed>
     */
    private function variables(
        InputPresentation $input,
    ): array {

        return [

            /*
            |--------------------------------------------------------------------------
            | Campo
            |--------------------------------------------------------------------------
            */

            'name' => $input->name,

            'label' => $input->label,

            'type' => $input->type,

            'placeholder' => $input->placeholder,

            'default' => $input->default,

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            'required' => $input->required,

            'readonly' => $input->readonly,

            'disabled' => $input->disabled,

            /*
            |--------------------------------------------------------------------------
            | Layout
            |--------------------------------------------------------------------------
            */

            'column_class' => $input->columnClass,

            /*
            |--------------------------------------------------------------------------
            | Metadatos del componente
            |--------------------------------------------------------------------------
            */

            'component' => $input->component->component,

            'blade_component' => $input->component->bladeComponent,

            'icon' => $input->component->icon,

            'css_class' => $input->component->cssClass,

            'binding' => $input->component->binding,

            'attributes' => $input->component->attributes,

        ];
    }
}
