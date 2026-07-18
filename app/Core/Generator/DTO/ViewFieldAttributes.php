<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

use App\Core\Generator\Enums\InputType;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * DTO que representa los atributos necesarios para renderizar
 * un componente del CN UI Framework.
 *
 * Es construido por ViewFieldProcessor y consumido por los
 * renderizadores de vistas Blade.
 *
 * No contiene lógica de negocio.
 *
 * @package App\Core\Generator\DTO
 * @since 2.0.0
 */
final readonly class ViewFieldAttributes
{
    /**
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(

        /*
        |--------------------------------------------------------------------------
        | Identificación
        |--------------------------------------------------------------------------
        */

        private string $name,

        private string $id,

        /*
        |--------------------------------------------------------------------------
        | Presentación
        |--------------------------------------------------------------------------
        */

        private string $label,

        private ?string $placeholder = null,

        private ?string $hint = null,

        /*
        |--------------------------------------------------------------------------
        | Componente
        |--------------------------------------------------------------------------
        */

        private string $component,

        private InputType $type,

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        private bool $required = false,

        private bool $readonly = false,

        private bool $disabled = false,

        /*
        |--------------------------------------------------------------------------
        | Valor
        |--------------------------------------------------------------------------
        */

        private ?string $value = null,

        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        private int $columns = 12,

        /*
        |--------------------------------------------------------------------------
        | Atributos adicionales
        |--------------------------------------------------------------------------
        */

        private array $attributes = [],
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Identificación
    |--------------------------------------------------------------------------
    */

    public function name(): string
    {
        return $this->name;
    }

    public function id(): string
    {
        return $this->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Presentación
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return $this->label;
    }

    public function placeholder(): ?string
    {
        return $this->placeholder;
    }

    public function hint(): ?string
    {
        return $this->hint;
    }

    /*
    |--------------------------------------------------------------------------
    | Componente
    |--------------------------------------------------------------------------
    */

    public function component(): string
    {
        return $this->component;
    }

    public function type(): InputType
    {
        return $this->type;
    }

    /*
    |--------------------------------------------------------------------------
    | Estado
    |--------------------------------------------------------------------------
    */

    public function required(): bool
    {
        return $this->required;
    }

    public function readonly(): bool
    {
        return $this->readonly;
    }

    public function disabled(): bool
    {
        return $this->disabled;
    }

    /*
    |--------------------------------------------------------------------------
    | Valor
    |--------------------------------------------------------------------------
    */

    public function value(): ?string
    {
        return $this->value;
    }

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    public function columns(): int
    {
        return $this->columns;
    }

    /*
    |--------------------------------------------------------------------------
    | Extras
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, scalar|null>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    private function buildAttributes(
        ColumnDefinition $column,
        string $variable
    ): ViewFieldAttributes {
        return new ViewFieldAttributes(

            name: $column->name(),

            id: $column->name(),

            label: $this->buildLabel($column),

            placeholder: $this->buildPlaceholder($column),

            hint: null,

            component: $this->buildComponent($column),

            type: $this->buildInputType($column),

            required: !$column->nullable(),

            readonly: false,

            disabled: false,

            value: $this->buildValue($column, $variable),

            columns: 12,

            attributes: $this->buildExtraAttributes($column),
        );
    }

    private function buildLabel(
        ColumnDefinition $column
    ): string {
        return str_replace(
            '_',
            ' ',
            ucwords($column->name(), '_')
        );
    }

    private function buildPlaceholder(
        ColumnDefinition $column
    ): ?string {
        return 'Ingrese ' . strtolower(
            $this->buildLabel($column)
        );
    }

    private function buildValue(
        ColumnDefinition $column,
        string $variable
    ): string {
        return sprintf(
            "{{ old('%s', $%s->%s ?? '') }}",
            $column->name(),
            $variable,
            $column->name()
        );
    }

    private function buildComponent(
        ColumnDefinition $column
    ): string {
        return $column
            ->type()
            ->inputType()
            ->componentName();
    }

    private function buildInputType(
        ColumnDefinition $column
    ): InputType {
        return $column
            ->type()
            ->inputType();
    }

    private function buildExtraAttributes(
        ColumnDefinition $column
    ): array {
        return [];
    }


}
