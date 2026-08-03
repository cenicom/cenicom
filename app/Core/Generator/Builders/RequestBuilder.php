<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Enums\FieldType;

final class RequestBuilder
{
    /**
     * Construye las variables utilizadas por los stubs
     * StoreRequest y UpdateRequest.
     *
     * @return array<string,mixed>
     */
    public function build(
        ModuleData $module,
        string $requestClass,
    ): array {

        return [

            'namespace' => $module->requestNamespace(),

            'request' => $requestClass,

            'model' => $module->modelClass(),

            'variable' => $module->variable(),

            'rules' => $this->buildValidationRules(
                $module
            ),

        ];
    }

    /**
     * Construye el bloque completo de reglas.
     */
    private function buildValidationRules(
        ModuleData $module,
    ): string {

        $rules = [];

        foreach ($module->columns() as $column) {

            if (! $column instanceof ColumnDefinition) {
                continue;
            }

            if (! $column->shouldGenerateValidation()) {
                continue;
            }

            $rules[] = $this->formatRules(
                $column->name(),
                $this->buildFieldRules($column)
            );
        }

        return implode(
            PHP_EOL . PHP_EOL,
            $rules
        );
    }

    /**
     * Construye las reglas de un campo.
     *
     * @return array<int,string>
     */
    private function buildFieldRules(
        ColumnDefinition $column,
    ): array {

        $rules = [];

        $required = $this->buildRequiredRule($column);

        if ($required !== null) {
            $rules[] = $required;
        }

        $type = $this->buildTypeRule($column);

        if ($type !== null) {
            $rules[] = $type;
        }

        $length = $this->buildLengthRule($column);

        if ($length !== null) {
            $rules[] = $length;
        }

        $unique = $this->buildUniqueRule($column);

        if ($unique !== null) {
            $rules[] = $unique;
        }

        return $rules;
    }

    private function buildRequiredRule(
        ColumnDefinition $column,
    ): string {

        return $column->nullable()
            ? 'nullable'
            : 'required';
    }

    private function buildTypeRule(
        ColumnDefinition $column,
    ): ?string {

        return match ($column->type()) {

            FieldType::STRING,
            FieldType::TEXT,
            FieldType::LONG_TEXT,
            FieldType::MEDIUM_TEXT,
            FieldType::CHAR
                => 'string',

            FieldType::INTEGER,
            FieldType::BIG_INTEGER,
            FieldType::SMALL_INTEGER,
            FieldType::TINY_INTEGER,
            FieldType::FOREIGN_ID
                => 'integer',

            FieldType::DECIMAL,
            FieldType::DOUBLE,
            FieldType::FLOAT
                => 'numeric',

            FieldType::BOOLEAN
                => 'boolean',

            FieldType::DATE,
            FieldType::DATETIME,
            FieldType::TIMESTAMP
                => 'date',

            FieldType::EMAIL
                => 'email',

            FieldType::UUID
                => 'uuid',

            FieldType::JSON
                => 'array',

            FieldType::ENUM
                => 'in:' . implode(
                    ',',
                    $column->enumValues()
                ),

            default => null,
        };
    }

    private function buildLengthRule(
        ColumnDefinition $column,
    ): ?string {

        if (! $column->hasLength()) {
            return null;
        }

        return 'max:' . $column->length();
    }

    private function buildUniqueRule(
        ColumnDefinition $column,
    ): ?string {

        if (! $column->unique()) {
            return null;
        }

        return 'unique';
    }

    /**
     * Convierte las reglas al formato Laravel.
     */
    private function formatRules(
        string $field,
        array $rules,
    ): string {

        $items = implode(
            "',\n                '",
            $rules
        );

        return <<<PHP
'{$field}' => [
                '{$items}',
            ],
PHP;
    }
}
