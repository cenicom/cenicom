<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Support\InvalidArgumentException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye el bloque de columnas de una migración.
 *
 * Transforma una colección de ColumnDefinition en código
 * PHP compatible con Blueprint para ser insertado en el
 * placeholder [[ fields ]] del migration.stub.
 *
 * Esta clase no conoce ModuleData, MigrationGenerator,
 * StubManager ni FileWriter.
 *
 * Su única responsabilidad es construir las columnas.
 *
 * @package App\Core\Generator\Support
 * @since 1.0.0
 */
final class ColumnBuilder
{
    /**
     * Construye la definición completa de una columna.
     *
     * @param ColumnDefinition $column
     * @return string
     */
    public function build(ColumnDefinition $column): string
    {
        return $this->applyModifiers(
            $this->buildColumn($column),
            $column
        );
    }

    /**
     * Construye una columna individual.
     */
    private function buildColumn(ColumnDefinition $column): string
    {
        return match ($column->type()) {
            'string' => $this->buildString($column),
            'integer' => $this->buildInteger($column),
            'boolean' => $this->buildBoolean($column),
            'uuid' => $this->buildUuid($column),
            'decimal' => $this->buildDecimal($column),
            'date' => $this->buildDate($column),
            'datetime' => $this->buildDateTime($column),
            'foreignId' => $this->buildForeignId($column),

            default => throw new InvalidArgumentException(
                "Tipo de columna no soportado: {$column->type()}"
            ),
        };
    }

    /**
     * Construye la definición base según el tipo.
     */
    private function buildDefinition(
        ColumnDefinition $column
    ): string {
        return match ($column->type()) {

            'string' => $this->buildString($column),

            'text' => $this->buildText($column),

            'integer' => $this->buildInteger($column),

            'boolean' => $this->buildBoolean($column),

            'uuid' => $this->buildUuid($column),

            'decimal' => $this->buildDecimal($column),

            'date' => $this->buildDate($column),

            'datetime' => $this->buildDateTime($column),

            'foreignId' => $this->buildForeignId($column),

            default => throw new InvalidArgumentException(
                "Tipo de columna no soportado: {$column->type()}"
            ),

        };
    }

    /**
     * Aplica modificadores a la columna.
     *
     * nullable()
     * default()
     * unique()
     * index()
     * unsigned()
     * comment()
     * constrained()
     * cascadeOnDelete()
     */
    private function applyModifiers(
        string $definition,
        ColumnDefinition $column
    ): string {
        if ($column->nullable()) {
            $definition .= '->nullable()';
        }

        if ($column->default() !== null) {
            $definition .= "->default('{$column->default()}')";
        }

        if ($column->unique()) {
            $definition .= '->unique()';
        }

        if ($column->index()) {
            $definition .= '->index()';
        }

        if ($column->unsigned()) {
            $definition .= '->unsigned()';
        }

        return $definition;
    }
    /**
     * Construye una columna string().
     */
    private function buildString(
        ColumnDefinition $column
    ): string {

        if ($column->length() !== null) {
            return sprintf(
                "\$table->string('%s', %d)",
                $column->name(),
                $column->length()
            );
        }

        return sprintf(
            "\$table->string('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna text().
     */
    private function buildText(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->text('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna integer().
     */
    private function buildInteger(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->integer('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna boolean().
     */
    private function buildBoolean(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->boolean('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna uuid().
     */
    private function buildUuid(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->uuid('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna decimal().
     */
    private function buildDecimal(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->decimal('%s', %d, %d)",
            $column->name(),
            $column->precision() ?? 10,
            $column->scale() ?? 2
        );
    }

    /**
     * Construye una columna date().
     */
    private function buildDate(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->date('%s')",
            $column->name()
        );
    }

    /**
     * Construye una columna dateTime().
     */
    private function buildDateTime(ColumnDefinition $column): string
    {
        return "\$table->dateTime('{$column->name()}')";
    }

    /**
     * Construye una columna foreignId().
     */
    private function buildForeignId(
        ColumnDefinition $column
    ): string {

        return sprintf(
            "\$table->foreignId('%s')",
            $column->name()
        );
    }
}
