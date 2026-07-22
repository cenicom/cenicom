<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Validators;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Validation\Contracts\ValidatorInterface;
use App\Core\Generator\Validation\Results\ValidationResult;





/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Valida la definición de campos del manifiesto.
 *
 * Comprueba la existencia, consistencia y estructura de las
 * columnas definidas para un módulo antes de permitir su
 * generación.
 *
 * Este validador únicamente procesa la colección de columnas.
 *
 * @package App\Core\Generator\Validation\Validators
 * @since 1.0.0
 */
final class FieldsValidator implements ValidatorInterface
{
    /**
     * Ejecuta la validación de los campos.
     */
    public function validate(
        ModuleSpecification $specification
    ): ValidationResult {
        $result = new ValidationResult();

        $columns = $specification->columns();

        if ($columns === []) {
            $result->addError(
                'Fields: at least one column must be defined.'
            );

            return $result;
        }

        $this->validateColumns($columns, $result);

        return $result;
    }

    /**
     * Valida la colección completa de columnas.
     *
     * @param array<ColumnDefinition> $columns
     */
    private function validateColumns(
        array $columns,
        ValidationResult $result,
    ): void {
        $names = [];

        foreach ($columns as $column) {

            $this->validateColumn(
                $column,
                $result
            );

            $this->validateDuplicateName(
                $column,
                $names,
                $result
            );
        }
    }

    /**
     * Valida una columna individual.
     */
    private function validateColumn(
        ColumnDefinition $column,
        ValidationResult $result,
    ): void {

        // siguientes reglas
        $this->validateName($column, $result);

        $this->validateType($column, $result);

        // nuevos bloques
        $this->validateLength($column, $result);

        $this->validatePrecision($column, $result);

        $this->validateScale($column, $result);

        $this->validateDefaultValue($column, $result);

        $this->validateNullable($column, $result);

        $this->validateUnique($column, $result);

        $this->validateIndex($column, $result);

        $this->validateForeignKey($column, $result);

        $this->validateEnum($column, $result);

        $this->validateCharset($column, $result);

        $this->validateCollation($column, $result);

        $this->validateComment($column, $result);

        $this->validateAfter($column, $result);

        $this->validateFirst($column, $result);

        // siguientes validaciones...

    }

    private function validateName(
        ColumnDefinition $column,
        ValidationResult $result,
    ): void {

        $name = trim($column->name());

        if ($name === '') {
            $result->addError(
                'Fields: column name is required.'
            );

            return;
        }

        if (strlen($name) > 64) {
            $result->addError(
                sprintf(
                    'Fields: column "%s" exceeds the maximum length.',
                    $name
                )
            );
        }
    }

    private function validateType(
        ColumnDefinition $column,
        ValidationResult $result,
    ): void {

        if (! $column->type() instanceof FieldType) {
            $result->addError(
                sprintf(
                    'Column "%s": invalid field type.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Detecta nombres duplicados.
     *
     * @param array<string> $names
     */
    private function validateDuplicateName(
        ColumnDefinition $column,
        array &$names,
        ValidationResult $result,
    ): void {

        $name = $column->name();

        if (in_array($name, $names, true)) {
            $result->addError(
                sprintf(
                    'Fields: duplicated column name "%s".',
                    $name
                )
            );

            return;
        }

        $names[] = $name;
    }

    /**
     * Valida la longitud del campo.
     */
    private function validateLength(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->hasLength()) {
            return;
        }

        if (! $column->type()->supportsLength()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support length.',
                    $column->name(),
                    $column->type()->value
                )
            );

            return;
        }

        if ($column->length() <= 0) {
            $result->addError(
                sprintf(
                    'Column "%s" length must be greater than zero.',
                    $column->name()
                )
            );
        }
    }
    /**
     * Valida la precisión del campo.
     */
    private function validatePrecision(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->hasPrecision()) {
            return;
        }

        if (! $column->type()->supportsPrecision()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support precision.',
                    $column->name(),
                    $column->type()->value
                )
            );

            return;
        }

        if ($column->precision() <= 0) {
            $result->addError(
                sprintf(
                    'Column "%s" precision must be greater than zero.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Valida la escala del campo.
     */
    private function validateScale(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->hasScale()) {
            return;
        }

        if (! $column->type()->supportsScale()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support scale.',
                    $column->name(),
                    $column->type()->value
                )
            );

            return;
        }

        if (! $column->hasPrecision()) {
            $result->addError(
                sprintf(
                    'Column "%s" defines a scale but no precision.',
                    $column->name()
                )
            );
        }

        if ($column->scale() < 0) {
            $result->addError(
                sprintf(
                    'Column "%s" scale must be zero or greater.',
                    $column->name()
                )
            );
        }

        if ($column->scale() > $column->precision()) {
            $result->addError(
                sprintf(
                    'Column "%s" scale cannot be greater than precision.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Valida el valor por defecto del campo.
     */
    private function validateDefaultValue(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->hasDefault()) {
            return;
        }

        if (! $column->type()->supportsDefault()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support default values.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }

        $default = $column->default();

        match ($column->phpType()) {

            'int' => is_int($default)
                || $result->addError(
                    sprintf(
                        'Column "%s" default value must be an integer.',
                        $column->name()
                    )
                ),

            'float' => (is_int($default) || is_float($default))
                || $result->addError(
                    sprintf(
                        'Column "%s" default value must be numeric.',
                        $column->name()
                    )
                ),

            'bool' => is_bool($default)
                || $result->addError(
                    sprintf(
                        'Column "%s" default value must be boolean.',
                        $column->name()
                    )
                ),

            'array' => is_array($default)
                || $result->addError(
                    sprintf(
                        'Column "%s" default value must be an array.',
                        $column->name()
                    )
                ),

            default => is_string($default)
                || $result->addError(
                    sprintf(
                        'Column "%s" default value must be a string.',
                        $column->name()
                    )
                ),
        };
    }

    /**
     * Valida el modificador nullable.
     */
    private function validateNullable(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->nullable()) {
            return;
        }

        if (! $column->type()->supportsNullable()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" cannot be nullable.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }
    }

    /**
     * Valida la restricción UNIQUE.
     */
    private function validateUnique(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->unique()) {
            return;
        }

        if (! $column->type()->supportsUnique()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support UNIQUE constraints.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }
    }

    /**
     * Valida la restricción INDEX.
     */
    private function validateIndex(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->index()) {
            return;
        }

        if (! $column->type()->supportsIndex()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support indexes.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }
    }

    /**
     * Valida la definición de una llave foránea.
     */
    private function validateForeignKey(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->isForeignKey()) {
            return;
        }

        if (! $column->type()->supportsForeignKey()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" cannot be used as a foreign key.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }

        if (! $column->hasReference()) {
            $result->addError(
                sprintf(
                    'Foreign key column "%s" requires a referenced column.',
                    $column->name()
                )
            );
        }

        if (! $column->hasTableReference()) {
            $result->addError(
                sprintf(
                    'Foreign key column "%s" requires a referenced table.',
                    $column->name()
                )
            );
        }

        if (
            $column->nullOnDelete()
            && ! $column->nullable()
        ) {
            $result->addError(
                sprintf(
                    'Column "%s" uses nullOnDelete() but is not nullable.',
                    $column->name()
                )
            );
        }

        $deleteRules = [
            $column->cascadeOnDelete(),
            $column->restrictOnDelete(),
            $column->nullOnDelete(),
        ];

        if (count(array_filter($deleteRules)) > 1) {
            $result->addError(
                sprintf(
                    'Foreign key column "%s" defines more than one ON DELETE action.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Valida la definición de un campo ENUM.
     */
    private function validateEnum(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if ($column->isEnum()) {

            if (! $column->hasEnumValues()) {
                $result->addError(
                    sprintf(
                        'ENUM column "%s" requires at least one value.',
                        $column->name()
                    )
                );
            }

            $values = $column->enumValues();

            if ($values === []) {
                $result->addError(
                    sprintf(
                        'ENUM column "%s" must define at least one value.',
                        $column->name()
                    )
                );
            }

            foreach ($values as $value) {

                if (! is_string($value) || trim($value) === '') {
                    $result->addError(
                        sprintf(
                            'ENUM column "%s" contains an invalid value.',
                            $column->name()
                        )
                    );
                }
            }

            if (count($values) !== count(array_unique($values))) {
                $result->addError(
                    sprintf(
                        'ENUM column "%s" contains duplicated values.',
                        $column->name()
                    )
                );
            }

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Campos no ENUM
    |--------------------------------------------------------------------------
    */

        if ($column->hasEnumValues()) {
            $result->addError(
                sprintf(
                    'Column "%s" is not an ENUM but defines enumValues.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Valida el charset de la columna.
     */
    private function validateCharset(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if ($column->charset() === null) {
            return;
        }

        if (! $column->type()->supportsCharset()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support charset.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }

        if (trim($column->charset()) === '') {
            $result->addError(
                sprintf(
                    'Column "%s" defines an empty charset.',
                    $column->name()
                )
            );
        }
    }

    /**
     * Valida la collation de la columna.
     */
    private function validateCollation(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if ($column->collation() === null) {
            return;
        }

        if (! $column->type()->supportsCollation()) {
            $result->addError(
                sprintf(
                    'Column "%s" of type "%s" does not support collation.',
                    $column->name(),
                    $column->type()->value
                )
            );
        }

        if (trim($column->collation()) === '') {
            $result->addError(
                sprintf(
                    'Column "%s" defines an empty collation.',
                    $column->name()
                )
            );
        }
    }

    private function validateComment(
        ColumnDefinition $column,
        ValidationResult $result,
    ): void {

        if (! $column->hasComment()) {
            return;
        }

        if (trim($column->comment()) === '') {
            $result->addError(
                sprintf(
                    'Column "%s": comment cannot be empty.',
                    $column->name()
                )
            );
        }
    }

    private function validateAfter(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->hasAfter()) {
            return;
        }

        $after = $column->after();

        if (trim($after) === '') {
            $result->addError(
                sprintf(
                    'Column "%s": after cannot be empty.',
                    $column->name()
                )
            );

            return;
        }

        if ($after === $column->name()) {
            $result->addError(
                sprintf(
                    'Column "%s": cannot reference itself in after.',
                    $column->name()
                )
            );
        }
    }

    private function validateFirst(
        ColumnDefinition $column,
        ValidationResult $result
    ): void {
        if (! $column->isFirstColumn()) {
            return;
        }

        if ($column->hasAfter()) {
            $result->addError(
                sprintf(
                    'Column "%s": first cannot be combined with after.',
                    $column->name()
                )
            );
        }
    }
}
