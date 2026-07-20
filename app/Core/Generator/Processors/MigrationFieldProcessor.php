<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\FieldType;

/**
 * Procesa la definición de campos de un módulo para generar
 * las instrucciones Blueprint de una migración Laravel.
 */
final class MigrationFieldProcessor
{
    /*
    |--------------------------------------------------------------------------
    | API pública
    |--------------------------------------------------------------------------
    */

    public function process(
        array $fields
    ): string {

        return implode(

            PHP_EOL . PHP_EOL,

            array_map(
                fn(ColumnDefinition $field) => $this->build($field),
                $fields
            )

        );
    }

    public function build(
        ColumnDefinition $field
    ): string {

        $column = $this->buildBase($field);

        $column = $this->applyModifiers(
            $column,
            $field
        );

        $column = $this->applyForeignKey(
            $column,
            $field
        );

        return $column . ';';
    }



    /*
    |--------------------------------------------------------------------------
    | Construcción de la columna
    |--------------------------------------------------------------------------
    */

    private function buildBase(
        ColumnDefinition $field
    ): string {
        return match ($field->type()) {

            FieldType::ID =>
            '$table->id()',

            FieldType::UUID =>
            sprintf(
                "\$table->uuid('%s')",
                $field->name()
            ),

            FieldType::STRING =>
            sprintf(
                "\$table->string('%s'%s)",
                $field->name(),
                $field->length()
                    ? ', ' . $field->length()
                    : ''
            ),

            FieldType::TEXT =>
            sprintf(
                "\$table->text('%s')",
                $field->name()
            ),

            FieldType::INTEGER =>
            sprintf(
                "\$table->integer('%s')",
                $field->name()
            ),

            FieldType::BIG_INTEGER =>
            sprintf(
                "\$table->bigInteger('%s')",
                $field->name()
            ),

            FieldType::BOOLEAN =>
            sprintf(
                "\$table->boolean('%s')",
                $field->name()
            ),

            FieldType::DATE =>
            sprintf(
                "\$table->date('%s')",
                $field->name()
            ),

            FieldType::DATETIME =>
            sprintf(
                "\$table->dateTime('%s')",
                $field->name()
            ),

            FieldType::TIME =>
            sprintf(
                "\$table->time('%s')",
                $field->name()
            ),

            FieldType::TIMESTAMP =>
            sprintf(
                "\$table->timestamp('%s')",
                $field->name()
            ),

            FieldType::FLOAT =>
            sprintf(
                "\$table->float('%s')",
                $field->name()
            ),

            FieldType::DOUBLE =>
            sprintf(
                "\$table->double('%s')",
                $field->name()
            ),

            FieldType::DECIMAL =>
            sprintf(
                "\$table->decimal('%s', %d, %d)",
                $field->name(),
                $field->precision() ?? 10,
                $field->scale() ?? 2
            ),

            FieldType::JSON =>
            sprintf(
                "\$table->json('%s')",
                $field->name()
            ),

            FieldType::ENUM =>
            sprintf(
                "\$table->enum('%s', %s)",
                $field->name(),
                var_export($field->enumValues(), true)
            ),

            FieldType::FOREIGN_ID =>
            sprintf(
                "\$table->foreignId('%s')",
                $field->name()
            ),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Modificadores Blueprint
    |--------------------------------------------------------------------------
    */

    private function applyModifiers(
        string $column,
        ColumnDefinition $field
    ): string;


    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    private function applyForeignKey(
        string $column,
        ColumnDefinition $field
    ): string {
        if (! $field->isForeignKey()) {
            return $column;
        }

        if ($field->constrained() === true) {
            $column .= '->constrained()';
        }

        if (is_string($field->constrained())) {
            $column .= sprintf(
                "->constrained('%s')",
                $field->constrained()
            );
        }

        if ($field->cascadeOnDelete()) {
            $column .= '->cascadeOnDelete()';
        }

        if ($field->cascadeOnUpdate()) {
            $column .= '->cascadeOnUpdate()';
        }

        if ($field->restrictOnDelete()) {
            $column .= '->restrictOnDelete()';
        }

        if ($field->nullOnDelete()) {
            $column .= '->nullOnDelete()';
        }

        return $column;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function formatDefaultValue(
        mixed $value
    ): string;
}
