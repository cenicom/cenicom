<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Resolvers;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\InputType;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Resuelve los metadatos de presentación de un campo del
 * CN Generator.
 *
 * Su responsabilidad consiste en traducir un ColumnDefinition
 * hacia el componente visual apropiado del CN UI Framework.
 *
 * No genera Blade.
 * No genera HTML.
 * No conoce Laravel.
 * No conoce Generators.
 *
 * Únicamente construye un ComponentMetadata.
 *
 * @package App\Core\Generator\Presentation\Resolvers
 * @since 2.0.0
 */
final readonly class ComponentResolver
{
    /**
     * Constructor.
     */
    public function __construct(
        private ColumnDefinition $column,
    ) {}

    /**
     * Obtiene los metadatos del componente.
     */
    public function resolve(): ComponentMetadata
    {
        return new ComponentMetadata(

            component: $this->component(),

            bladeComponent: $this->blade(),

            cssClass: $this->cssClass(),

            columnClass: $this->columnClass(),

            binding: $this->binding(),

            icon: $this->icon(),

            placeholder: $this->placeholder(),

            attributes: $this->attributes(),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Componentes
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el nombre lógico del componente.
     */
    private function component(): string
    {
        return match ($this->column->inputType()) {

            InputType::TEXT => 'input',

            InputType::NUMBER => 'number',

            InputType::TEXTAREA => 'textarea',

            InputType::SELECT => 'select',

            InputType::CHECKBOX => 'checkbox',

            InputType::DATE => 'date',

            InputType::TIME => 'time',

            InputType::DATETIME_LOCAL => 'datetime',

            default => 'input',
        };
    }

    /**
     * Obtiene el componente Blade.
     */
    private function blade(): string
    {
        return 'x-cn.{component}' . $this->component();
    }

    /*
    |--------------------------------------------------------------------------
    | Apariencia
    |--------------------------------------------------------------------------
    */

    /**
     * Clase CSS sugerida.
     */
    private function cssClass(): string
    {
        return '';
    }

    /**
     * Clase Bootstrap de la columna.
     */
    private function columnClass(): string
    {
        return match ($this->column->inputType()) {

            InputType::TEXTAREA
                => ComponentMetadata::COL_FULL,

            default
                => ComponentMetadata::COL_HALF,
        };
    }

    /**
     * Binding sugerido.
     */
    private function binding(): string
    {
        return '';
    }

    /**
     * Placeholder sugerido.
     */
    private function placeholder(): string
    {
        return 'Enter ' . $this->label();
    }

    /**
     * Icono sugerido.
     */
    private function icon(): string
    {
        return match ($this->column->inputType()) {

            InputType::TEXT
                => 'bi-type',

            InputType::NUMBER
                => 'bi-123',

            InputType::TEXTAREA
                => 'bi-card-text',

            InputType::SELECT
                => 'bi-list',

            InputType::CHECKBOX
                => 'bi-check-square',

            InputType::DATE
                => 'bi-calendar',

            InputType::TIME
                => 'bi-clock',

            InputType::DATETIME_LOCAL
                => 'bi-calendar-event',

            default
                => 'bi-input-cursor-text',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene los atributos adicionales del componente.
     *
     * @return array<string,mixed>
     */
    private function attributes(): array
    {
        $attributes = [];

        if ($this->column->hasLength()) {
            $attributes['maxlength'] = $this->column->length();
        }

        if ($this->column->hasPrecision()) {
            $attributes['precision'] = $this->column->precision();
        }

        if ($this->column->hasScale()) {
            $attributes['step'] = $this->buildStep();
        }

        return $attributes;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Construye el label legible del campo.
     */
    private function label(): string
    {
        return ucwords(
            str_replace('_', ' ', $this->column->name())
        );
    }

    /**
     * Calcula el atributo HTML "step".
     */
    private function buildStep(): string
    {
        $scale = $this->column->scale();

        if ($scale === null || $scale <= 0) {
            return '1';
        }

        return '0.' . str_repeat('0', $scale - 1) . '1';
    }
}
