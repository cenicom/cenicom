<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\InputType;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;
use App\Core\Generator\Presentation\InputPresentation;
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
    private const RENDERERS = [

        InputType::TEXT->value      => 'buildString',

        InputType::TEXTAREA->value  => 'buildTextarea',

        InputType::NUMBER->value    => 'buildNumber',

        InputType::CHECKBOX->value  => 'buildCheckbox',

        InputType::DATE->value      => 'buildDate',

        InputType::SELECT->value    => 'buildSelect',

    ];

    public function __construct(
        private readonly InputPresentation $presentation,
    ) {}

    private function metadata(
        ColumnDefinition $field
    ): ComponentMetadata {
        return $this->presentation->for(
            $field->inputType()
        );
    }

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

        $metadata = $this->metadata($field);

        $builder = self::RENDERERS[$field->inputtype()->value] ?? null;

        if ($builder === null) {

            throw new InvalidArgumentException(
                sprintf(
                    'No existe un renderer registrado para el InputType [%s].',
                    $field->inputType()->value
                )
            );
        }

        return $this->{$builder}(
            $field,
            $metadata,
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
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return $this->buildInput(
            $field,
            $metadata,
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
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return $this->component(
            $metadata->blade(),
            [

                'name' => $field->name(),

                'label' => $this->label($field),

                'placeholder' => sprintf(
                    $metadata->placeholder,
                    strtolower($this->label($field))
                ),

                ':value' => $this->value($field, $variable),

            ]
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */
    private function buildNumber(
        ColumnDefinition $field,
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return $this->buildInput(

            $field,

            $metadata,

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
        ComponentMetadata $metadata,
        string $variable
    ): string {

        $step = '0.01';

        if ($field->scale() !== null) {

            $step = '0.'
                . str_repeat('0', $field->scale() - 1)
                . '1';
        }

        return $this->buildInput(

            $field,

            $metadata,

            $variable,

            [

                'type' => 'number',

                'step' => $step,

            ]

        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */

    private function buildCheckbox(
        ColumnDefinition $field,
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return $this->component(
            $metadata->blade(),
            [

                'name' => $field->name(),

                'label' => $this->label($field),

                ':checked' => sprintf(
                    "old('%s', \$%s->%s ?? false)",
                    $field->name(),
                    $variable,
                    $field->name()
                ),

            ]
        );
    }

    /**
     * Número de columnas del campo.
     *
     * @param ColumnDefinition $field
     */

    private function buildDate(
        ColumnDefinition $field,
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return $this->buildInput(

            $field,

            $metadata,

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
        ComponentMetadata $metadata,
        string $variable
    ): string {

        return sprintf(
            "<%s\n    name=\"%s\"\n    label=\"%s\">\n\n    {{-- Opciones generadas posteriormente --}}\n\n</%s>",
            $metadata->blade(),
            $field->name(),
            $this->label($field),
            $metadata->blade()
        );
    }

    private function buildInput(
        ColumnDefinition $field,
        ComponentMetadata $metadata,
        string $variable,
        array $attributes = []
    ): string {

        $attributes = array_merge(

            $metadata->attributes,

            [

                'name' => $field->name(),

                'label' => $this->label($field),

                ':value' => $this->value($field, $variable),

            ],

            $attributes

        );

        if (
            $metadata->placeholder !== ''
            && $field->inputType()->supportsPlaceholder()
        ) {
            $attributes['placeholder'] = sprintf(
                $metadata->placeholder,
                strtolower($this->label($field))
            );
        }

        if ($this->isRequired($field)) {

            $attributes['required'] = null;
        }

        return $this->component(

            $metadata->blade(),

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

            if ($value === '') {
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
