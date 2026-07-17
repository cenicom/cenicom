<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa la definición de una columna de base de datos.
 *
 * Contiene toda la información necesaria para que el
 * MigrationGenerator construya automáticamente el esquema
 * de una tabla.
 *
 * @package App\Core\Generator\DTO
 * @since 1.0.0
 */
final readonly class ColumnDefinition
{
    public function __construct(
        private string $name,
        private string $type,
        private ?int $length = null,
        private ?int $precision = null,
        private ?int $scale = null,
        private bool $nullable = false,
        private mixed $default = null,
        private bool $unique = false,
        private bool $index = false,
        private bool $unsigned = false,
        private ?string $references = null,
        private ?string $on = null,
        private ?string $comment = null,
    ) {
    }

    /**
     * Construye una ColumnDefinition a partir de un arreglo.
     *
     * @param array<string,mixed> $definition
     */
    public static function fromArray(array $definition): self
    {
        if (!isset($definition['name'])) {
            throw new \InvalidArgumentException(
                'The column definition requires the "name" attribute.'
            );
        }

        if (!isset($definition['type'])) {
            throw new \InvalidArgumentException(
                'The column definition requires the "type" attribute.'
            );
        }

        return new self(
            name: (string) $definition['name'],
            type: (string) $definition['type'],
            length: isset($definition['length'])
            ? (int) $definition['length']
            : null,
            precision: isset($definition['precision'])
            ? (int) $definition['precision']
            : null,
            scale: isset($definition['scale'])
            ? (int) $definition['scale']
            : null,
            nullable: (bool) ($definition['nullable'] ?? false),
            default: $definition['default'] ?? null,
            unique: (bool) ($definition['unique'] ?? false),
            index: (bool) ($definition['index'] ?? false),
            unsigned: (bool) ($definition['unsigned'] ?? false),
            references: $definition['references'] ?? null,
            on: $definition['on'] ?? null,
            comment: $definition['comment'] ?? null,
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function length(): ?int
    {
        return $this->length;
    }

    public function precision(): ?int
    {
        return $this->precision;
    }

    public function scale(): ?int
    {
        return $this->scale;
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }

    public function default(): mixed
    {
        return $this->default;
    }

    public function unique(): bool
    {
        return $this->unique;
    }

    public function index(): bool
    {
        return $this->index;
    }

    public function unsigned(): bool
    {
        return $this->unsigned;
    }

    public function references(): ?string
    {
        return $this->references;
    }

    public function on(): ?string
    {
        return $this->on;
    }

    public function comment(): ?string
    {
        return $this->comment;
    }
}
