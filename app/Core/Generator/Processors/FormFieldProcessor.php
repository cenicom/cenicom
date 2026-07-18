<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

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
     * Procesa todos los campos del módulo.
     *
     * @param array<int,array<string,mixed>> $fields
     */
    public function process(
        array $fields,
        string $variable
    ): string {

        $components = [];

        foreach ($fields as $field) {

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

    /**
     * Construye un componente Blade.
     *
     * @param array<string,mixed> $field
     */
    private function buildField(
        array $field,
        string $variable
    ): string {

        $builder = self::BUILDERS[
            $field['type']
        ] ?? null;

        if ($builder === null) {

            throw new InvalidArgumentException(
                sprintf(
                    'Tipo [%s] no soportado.',
                    $field['type']
                )
            );

        }

        return $this->{$builder}(
            $field,
            $variable
        );
    }

    //Helpers

    /**
     * Obtiene la etiqueta del campo.
     *
     * @param array<string,mixed> $field
     */
    private function label(
        array $field
    ): string {

        if (! empty($field['label'])) {
            return (string) $field['label'];
        }

        return ucfirst(
            str_replace(
                '_',
                ' ',
                $field['name']
            )
        );
    }

    /**
     * Obtiene el placeholder del campo.
     *
     * @param array<string,mixed> $field
     */
    private function placeholder(
        array $field
    ): string {

        if (! empty($field['placeholder'])) {
            return (string) $field['placeholder'];
        }

        return sprintf(
            'Ingrese %s',
            strtolower($this->label($field))
        );
    }

    /**
     * Genera el valor Blade del componente.
     *
     * @param array<string,mixed> $field
     */
    private function value(
        array $field,
        string $variable
    ): string {

        return sprintf(
            "old('%s', \$%s->%s ?? '')",
            $field['name'],
            $variable,
            $field['name']
        );
    }

    /**
     * Determina si el campo es obligatorio.
     *
     * @param array<string,mixed> $field
     */
    private function isRequired(
        array $field
    ): bool {

        return !(
            $field['nullable']
            ?? false
        );
    }

    /**
     * Obtiene el texto de ayuda.
     *
     * @param array<string,mixed> $field
     */
    private function hint(
        array $field
    ): string {

        return (string) (
            $field['help']
            ?? ''
        );
    }

    /**
     * Determina si el campo es solo lectura.
     *
     * @param array<string,mixed> $field
     */
    private function readonly(
        array $field
    ): bool {

        return (bool) (
            $field['readonly']
            ?? false
        );
    }

    /**
     * Determina si el campo está deshabilitado.
     *
     * @param array<string,mixed> $field
     */
    private function disabled(
        array $field
    ): bool {

        return (bool) (
            $field['disabled']
            ?? false
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param array<string,mixed> $field
     */
    private function columns(
        array $field
    ): int {

        return (int) (
            $field['columns']
            ?? 1
        );
    }
    // Builders...

    private function buildString(
        array $field,
        string $variable
    ): string {
        $required = $this->isRequired($field)
            ? "\n    required"
            : '';

        return <<<BLADE
<x-cn.input
    name="{$field['name']}"
    label="{$this->label($field)}"
    placeholder="{$this->placeholder($field)}"
    :value="{$this->value($field, $variable)}"
    required="{$required}"
/>
BLADE;

    }

    private function buildTextarea(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.textarea
    name="{$field['name']}"
    label="{$this->label($field)}"
    placeholder="{$this->placeholder($field)}"
    :value="{$this->value($field, $variable)}"
/>
BLADE;

    }

    private function buildNumber(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.input
    type="number"
    name="{$field['name']}"
    label="{$this->label($field)}"
    :value="{$this->value($field, $variable)}"
/>
BLADE;

    }

    private function buildDecimal(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.input
    type="number"
    step="0.01"
    name="{$field['name']}"
    label="{$this->label($field)}"
    :value="{$this->value($field, $variable)}"
/>
BLADE;
        //tpdo -> {
        //"precision": 10,
        //"scale": 4
        //    }
    }

    private function buildCheckbox(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.checkbox
    name="{$field['name']}"
    label="{$this->label($field)}"
    :checked="old('{$field['name']}', \${$variable}->{$field['name']} ?? false)"
/>
BLADE;

    }

    private function buildDate(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.input
    type="date"
    name="{$field['name']}"
    label="{$this->label($field)}"
    :value="{$this->value($field, $variable)}"
/>
BLADE;

    }

    private function buildSelect(
        array $field,
        string $variable
    ): string {

        return <<<BLADE
<x-cn.select
    name="{$field['name']}"
    label="{$this->label($field)}">

    {{-- Opciones generadas posteriormente --}}

</x-cn.select>
BLADE;

    }




}
