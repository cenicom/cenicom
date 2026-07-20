<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use InvalidArgumentException;

/**
 * Procesa la definición de campos de un módulo para generar
 * automáticamente los componentes Blade del formulario.
 */
final class FormFieldProcessor
{
    /**
     * Relación tipo de dato → método constructor.
     */
    private const BUILDERS = [

        'string' => 'buildString',

        'text' => 'buildTextarea',

        'integer' => 'buildNumber',

        'boolean' => 'buildCheckbox',

        'decimal' => 'buildDecimal',

        'date' => 'buildDate',

        'foreignId' => 'buildSelect',

    ];

    /**
     * @param ColumnDefinition[] $fields
     */
    /**
     * @param ColumnDefinition[] $fields
     */
    public function process(
        array $fields,
        string $variable
    ): string {

        $components = [];

        foreach ($fields as $field) {

            if (!$field->shouldAppearInForm()) {
                continue;
            }

            $components[] = $this->buildField(
                $field,
                $variable
            );
        }

        return implode(
            PHP_EOL . PHP_EOL,
            $components
        );
    }


    private function buildField(
        ColumnDefinition $field,
        string $variable
    ): string {

        $builder = self::BUILDERS[$field->type()->value] ?? null;

        if ($builder === null) {

            throw new InvalidArgumentException(
                sprintf(
                    'Tipo [%s] no soportado.',
                    $field->type()->value
                )
            );
        }

        return $this->{$builder}(
            $field,
            $variable
        );
    }

    //Helpers


    private function label(
        ColumnDefinition $field
    ): string {

        return ucfirst(
            str_replace(
                '_',
                ' ',
                $field->name()
            )
        );
    }


    private function placeholder(
        ColumnDefinition $field
    ): string {

        return sprintf(
            'Ingrese %s',
            strtolower($this->label($field))
        );
    }


    private function value(
        ColumnDefinition $field,
        string $variable
    ): string {

        return sprintf(
            "old('%s', \$%s->%s ?? '')",
            $field->name(),
            $variable,
            $field->name()
        );
    }

    private function isRequired(
        ColumnDefinition $field
    ): bool {

        return !$field->nullable();
    }

    // Builders...

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */

    private function buildString(
        ColumnDefinition $field,
        string $variable
    ): string {

        return $this->buildInput(
            $field,
            $variable
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */
    private function buildTextarea(
        ColumnDefinition $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.textarea
    name="{$field->name()}"
    label="{$this->label($field)}"
    placeholder="{$this->placeholder($field)}"
    :value="{$this->value($field,$variable)}"
/>
BLADE;
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */
    private function buildNumber(
        ColumnDefinition $field,
        string $variable
    ): string {

        return $this->buildInput(
            $field,
            $variable,
            [
                'type' => 'number',
            ]
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */
    private function buildDecimal(
        ColumnDefinition $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.input
    type="number"
    step="0.0001"
    name="{$field->name()}"
    label="{$this->label($field)}"
    :value="{$this->value($field,$variable)}"
/>
BLADE;
        if ($field->scale() !== null) {

            $step = '0.' .
                str_repeat('0', $field->scale() - 1) .
                '1';
        }
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */

    private function buildCheckbox(
        ColumnDefinition $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.checkbox
    name="{$field->name()}"
    label="{$this->label($field)}"
    :checked="old('{$field->name()}', \${$variable}->{$field->name()} ?? false)"
/>
BLADE;
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */

    private function buildDate(
        ColumnDefinition $field,
        string $variable
    ): string {

        return $this->buildInput(
            $field,
            $variable,
            [
                'type' => 'date',
            ]
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */
    private function buildSelect(
        ColumnDefinition $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.select
    name="{$field->name()}"
    label="{$this->label($field)}">

    {{-- Opciones generadas posteriormente --}}

</x-cn.select>
BLADE;
    }

    private function buildInput(
        ColumnDefinition $field,
        string $variable,
        array $attributes = []
    ): string {

        $attributes = array_merge([
            'name' => $field->name(),
            'label' => $this->label($field),
            ':value' => $this->value($field, $variable),
            'placeholder' => $this->placeholder($field),
        ], $attributes);

        if ($this->isRequired($field)) {
            $attributes['required'] = null;
        }

        return $this->component(
            'x-cn.input',
            $attributes
        );
    }

    private function component(
        string $component,
        array $attributes
    ): string {

        $lines = [];

        foreach ($attributes as $name => $value) {

            if ($value === null) {
                $lines[] = "    {$name}";
                continue;
            }

            $lines[] = sprintf(
                '    %s="%s"',
                $name,
                $value
            );
        }

        return sprintf(
            "<%s\n%s\n/>",
            $component,
            implode(PHP_EOL, $lines)
        );
    }
}
