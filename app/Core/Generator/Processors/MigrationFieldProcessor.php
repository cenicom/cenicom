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

    /**
     * Procesa todas las columnas del módulo.
     *
     * @param ColumnDefinition[] $fields
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
    ): string {

        $column = $this->applyNullable($column, $field);

        $column = $this->applyDefault($column, $field);

        $column = $this->applyUnsigned($column, $field);

        $column = $this->applyUnique($column, $field);

        $column = $this->applyIndex($column, $field);

        $column = $this->applyComment($column, $field);

        $column = $this->applyCharset($column, $field);

        $column = $this->applyCollation($column, $field);

        $column = $this->applyAfter($column, $field);

        $column = $this->applyFirst($column, $field);

        return $column;
    }

    private function applyUnsigned(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->unsigned()) {

            $column .= '->unsigned()';
        }

        return $column;
    }

    private function applyComment(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->comment() === null) {
            return $column;
        }

        $column .= sprintf(
            "->comment('%s')",
            addslashes($field->comment())
        );

        return $column;
    }

    private function applyCharset(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->charset() === null) {
            return $column;
        }

        $column .= sprintf(
            "->charset('%s')",
            $field->charset()
        );

        return $column;
    }

    private function applyCollation(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->collation() === null) {
            return $column;
        }

        $column .= sprintf(
            "->collation('%s')",
            $field->collation()
        );

        return $column;
    }

    private function applyAfter(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->after() === null) {
            return $column;
        }

        $column .= sprintf(
            "->after('%s')",
            $field->after()
        );

        return $column;
    }

    private function applyFirst(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->first()) {

            $column .= '->first()';
        }

        return $column;
    }

    private function applyNullable(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->nullable()) {

            $column .= '->nullable()';
        }

        return $column;
    }

    private function applyDefault(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->default() === null) {
            return $column;
        }

        $column .= sprintf(
            '->default(%s)',
            $this->formatDefaultValue(
                $field->default()
            )
        );

        return $column;
    }

    private function applyUnique(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->unique()) {

            $column .= '->unique()';
        }

        return $column;
    }

    private function applyIndex(
        string $column,
        ColumnDefinition $field
    ): string {

        if ($field->index()) {

            $column .= '->index()';
        }

        return $column;
    }

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
    ): string {

        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {

            return sprintf(
                "'%s'",
                addslashes($value)
            );
        }

        if (is_array($value)) {

            return var_export(
                $value,
                true
            );
        }

        if ($value instanceof \BackedEnum) {

            return $this->formatDefaultValue(
                $value->value
            );
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Tipo de valor por defecto no soportado [%s].',
                get_debug_type($value)
            )
        );
    }
}
