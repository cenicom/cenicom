<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Presenters;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Presentation\Contracts\PresentationInterface;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;
use App\Core\Generator\Presentation\DTO\InputPresentation;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Presenter encargado de transformar una ColumnDefinition
 * en una representación visual del formulario.
 *
 * No genera Blade.
 * No genera HTML.
 * No conoce Laravel.
 *
 * Su única responsabilidad es construir un InputPresentation.
 *
 * @package App\Core\Generator\Presentation\Presenters
 * @since 2.0.0
 */
final readonly class ColumnPresenter implements PresentationInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private ColumnDefinition $column,
    ) {}

    /**
     * Genera la representación del campo.
     */
    public function present(): InputPresentation
{
    $component =
        $this->buildComponent();

    return new InputPresentation(

        name: $this->column->name(),

        label: $this->buildLabel(),

        type: $this->column->type()->value,

        placeholder: $component->placeholder,

        component: $component,

        required: $this->isRequired(),

        readonly: $this->isReadonly(),

        disabled: $this->isDisabled(),

        default: $this->column->default(),

        columnClass: $component->columnClass,

    );
}

    /*
    |--------------------------------------------------------------------------
    | Builders
    |--------------------------------------------------------------------------
    */

    /**
     * Construye el label del campo.
     */
    private function buildLabel(): string
    {
        return ucwords(
            str_replace('_', ' ', $this->column->name())
        );
    }

    /**
     * Construye el placeholder sugerido.
     */
    private function buildPlaceholder(): string
    {
        return 'Enter '.$this->buildLabel();
    }

    /**
     * Construye los metadatos del componente.
     */
    private function buildComponent(): ComponentMetadata
    {
        /*
         * En la siguiente misión este método delegará
         * al ComponentResolver.
         */

        return new ComponentMetadata(

            component: 'input',

            bladeComponent: 'x-cn.input',

            cssClass: '',

            columnClass: ComponentMetadata::COL_HALF,

            binding: '',

            icon: '',

            placeholder: $this->buildPlaceholder(),

            attributes: [],
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Estado
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el campo es obligatorio.
     */
    private function isRequired(): bool
    {
        return ! $this->column->nullable();
    }

    /**
     * Determina si el campo es de solo lectura.
     */
    private function isReadonly(): bool
    {
        return
            $this->column->isPrimaryKey()
            || ! $this->column->shouldBeEditable();
    }

    /**
     * Determina si el campo debe renderizarse deshabilitado.
     */
    private function isDisabled(): bool
    {
        return false;
    }
}
