<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use InvalidArgumentException;

/**
 * Procesa la definición de campos de un módulo para generar
 * las instrucciones Blueprint de una migración Laravel.
 */
final class MigrationFieldProcessor
{
    private const INDENT = '            ';
    private const FIELD_NULLABLE = 'nullable';
    private const FIELD_DEFAULT = 'default';
    private const FIELD_UNIQUE = 'unique';
    private const FIELD_INDEX = 'index';
    private const FIELD_COMMENT = 'comment';

    private const BUILDERS = [

        'id' => 'buildId',

        'uuid' => 'buildUuid',

        'string' => 'buildString',

        'char' => 'buildChar',

        'text' => 'buildText',

        'longText' => 'buildTextarea',

        'integer' => 'buildInteger',

        'bigInteger' => 'buildBigInteger',

        'unsignedInteger' => 'buildUnsignedInteger',

        'unsignedBigInteger' => 'buildUnsignedBigInteger',

        'boolean' => 'buildBoolean',

        'decimal' => 'buildDecimal',

        'double' => 'buildDouble',

        'float' => 'buildFloat',

        'date' => 'buildDate',

        'datetime' => 'buildDateTime',

        'timestamp' => 'buildTimestamp',

        'time' => 'buildTime',

        'json' => 'buildJson',

        'enum' => 'buildEnum',

        'foreignId' => 'buildForeignId',



    ];

    /**
     * Procesa la colección de campos.
     *
     * @param array<int, array<string, mixed>> $fields
     */
    public function process(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $lines[] = $this->buildColumn($field);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Construye la instrucción Blueprint correspondiente.
     *
     * @param array<string, mixed> $field
     */
    private function buildColumn(array $field): string
    {
        return $this->resolveColumn($field);
    }

    private function resolveColumn(
        array $field
    ): string {

        $builder = self::BUILDERS[$field['type']] ?? null;

        if ($builder === null) {

            throw new InvalidArgumentException(
                sprintf(
                    'Tipo [%s] no soportado.',
                    $field['type']
                )
            );

        }

        return $this->{$builder}($field);

    }

    /**
     * Genera una columna integer().
     *
     * @param array<string, mixed> $field
     */
    private function buildInteger(array $field): string
    {
        $column = sprintf(
            "%s\$table->integer('%s')",
            self::INDENT,
            $field['name']
        );

        return $this->appendModifiers(
            rtrim($column, ';'),
            $field
        );
    }

    /**
     * Genera una columna bigInteger().
     *
     * @param array<string, mixed> $field
     */
    private function buildBigInteger(array $field): string
    {
        return $this->buildSimpleColumn(
            'bigInteger',
            $field
        );
    }

    private function buildSimpleColumn(
        string $method,
        array $field
    ): string {

        $column = sprintf(
            "%s\$table->%s('%s')",
            self::INDENT,
            $method,
            $field['name']
        );

        return $this->appendModifiers(
            $column,
            $field
        );
    }

    /**
     * Genera una columna boolean().
     *
     * @param array<string, mixed> $field
     */
    private function buildBoolean(array $field): string
    {
        return $this->buildSimpleColumn(
            'boolean',
            $field
        );
    }

    /**
     * Genera una columna decimal().
     *
     * @param array<string, mixed> $field
     */
    private function buildDecimal(array $field): string
    {
        $precision = (int) ($field['precision'] ?? 8);
        $scale = (int) ($field['scale'] ?? 2);

        $column = sprintf(
            "%s\$table->decimal('%s', %d, %d)",
            self::INDENT,
            $field['name'],
            $precision,
            $scale
        );

        return $this->appendModifiers(
            rtrim($column, ';'),
            $field
        );
    }

    /**
     * Genera una columna id().
     */
    private function buildId(): string
    {
        return '            $table->id();';
    }

    /**
     * Genera una columna Uuid().
     */
    private function buildUuid(
        array $field
    ): string {

        $column = sprintf(
            "%s\$table->uuid('%s')",
            self::INDENT,
            $field['name']
        );

        return $this->appendModifiers(
            $column,
            $field
        );
    }

    /**
     * Genera una columna string().
     *
     * @param array<string, mixed> $field
     */
    private function buildString(array $field): string
    {
        $column = isset($field['length'])
            ? sprintf(
                "%s\$table->string('%s', %d)",
                self::INDENT,
                $field['name'],
                (int) $field['length']
            )
            : sprintf(
                "%s\$table->string('%s')",
                self::INDENT,
                $field['name']
            );

        return $this->appendModifiers(
            $column,
            $field
        );
    }

    /**
     * Genera una columna text().
     *
     * @param array<string, mixed> $field
     */
    private function buildText(array $field): string
    {
        $column = sprintf(
            "%s\$table->text('%s')",
            self::INDENT,
            $field['name']
        );

        return $this->appendModifiers(
            rtrim($column, ';'),
            $field
        );
    }

    /**
     * Agrega los modificadores Blueprint a una columna.
     *
     * @param array<string, mixed> $field
     */
    private function appendModifiers(
        string $column,
        array $field
    ): string {
        if (($field[self::FIELD_NULLABLE] ?? false) === true) {
            $column .= '->nullable()';
        }

        if (
            array_key_exists(
                self::FIELD_DEFAULT,
                $field
            )
        ) {
            $column .= sprintf(
                '->default(%s)',
                $this->formatDefaultValue($field[self::FIELD_DEFAULT])
            );
        }

        if (($field[self::FIELD_UNIQUE] ?? false) === true) {
            $column .= '->unique()';
        }

        if (($field[self::FIELD_INDEX] ?? false) === true) {
            $column .= '->index()';
        }

        if (!empty($field[self::FIELD_COMMENT])) {
            $column .= sprintf(
                "->comment('%s')",
                addslashes((string) $field[self::FIELD_COMMENT])
            );
        }

        return $column . ';';
    }

    /**
     * Formatea el valor por defecto para Blueprint.
     */
    private function formatDefaultValue(
        mixed $value
    ): string {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',

            is_numeric($value) => (string) $value,

            $value === null => 'null',

            default => sprintf(
                "'%s'",
                addslashes((string) $value)
            ),
        };
    }

    /**
     * Genera una columna foreignId().
     *
     * @param array<string, mixed> $field
     */
    private function buildForeignId(array $field): string
    {
        $column = sprintf(
            "            \$table->foreignId('%s')",
            $field['name']
        );

        $column = $this->appendRelationshipModifiers(
            $column,
            $field
        );

        return $this->appendModifiers(
            $column,
            $field
        );
    }

    /**
     * Agrega modificadores de relaciones.
     *
     * @param array<string, mixed> $field
     */
    private function appendRelationshipModifiers(
        string $column,
        array $field
    ): string {
        if (array_key_exists('constrained', $field)) {

            if ($field['constrained'] === true) {
                $column .= '->constrained()';
            } elseif (is_string($field['constrained'])) {
                $column .= sprintf(
                    "->constrained('%s')",
                    $field['constrained']
                );
            }
        }

        if (($field['cascadeOnDelete'] ?? false) === true) {
            $column .= '->cascadeOnDelete()';
        }

        if (($field['cascadeOnUpdate'] ?? false) === true) {
            $column .= '->cascadeOnUpdate()';
        }

        if (($field['restrictOnDelete'] ?? false) === true) {
            $column .= '->restrictOnDelete()';
        }

        if (($field['nullOnDelete'] ?? false) === true) {
            $column .= '->nullOnDelete()';
        }

        return $column;
    }

    private function buildDate(array $field): string
    {
        return $this->buildSimpleColumn(
            'date',
            $field
        );
    }

    private function buildFloat(array $field): string
    {
        return $this->buildSimpleColumn(
            'float',
            $field
        );
    }

    private function buildJson(array $field): string
    {
        return $this->buildSimpleColumn(
            'json',
            $field
        );
    }


}
