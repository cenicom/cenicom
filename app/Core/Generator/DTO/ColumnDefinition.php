<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Enums\InputType;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Value Object que representa la definición completa de un
 * campo de un módulo del CN Generator.
 *
 * Centraliza toda la información necesaria para generar
 * automáticamente:
 *
 * - Migraciones
 * - Modelos
 * - Formularios
 * - Validaciones
 * - Tablas
 * - Requests
 * - Resources
 * - API
 *
 * Esta clase constituye el núcleo semántico del sistema de
 * generación automática de código.
 *
 * @package App\Core\Generator\DTO
 * @since 2.0.0
 */
final readonly class ColumnDefinition
{
    /*
    |--------------------------------------------------------------------------
    | Identidad
    |--------------------------------------------------------------------------
    */

    private string $name;

    private FieldType $type;

    private ?InputType $inputType;

    private ?string $charset;

    private ?string $collation;

    /*
    |--------------------------------------------------------------------------
    | Definición física
    |--------------------------------------------------------------------------
    */

    private ?int $length;

    private ?int $precision;

    private ?int $scale;

    private bool $nullable;

    private mixed $default;

    private bool $unsigned;

    /*
    |--------------------------------------------------------------------------
    | Restricciones
    |--------------------------------------------------------------------------
    */

    private bool $unique;

    private bool $index;

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    private ?string $references;

    private ?string $on;

    /**
     * false              -> sin constrained
     * true               -> ->constrained()
     * 'users'            -> ->constrained('users')
     */
    private bool|string $constrained;

    private bool $cascadeOnDelete;

    private bool $cascadeOnUpdate;

    private bool $restrictOnDelete;

    private bool $nullOnDelete;

    /*
    |--------------------------------------------------------------------------
    | Tipos especiales
    |--------------------------------------------------------------------------
    */

    /**
     * @var array<int,string>
     */
    private array $enumValues;

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    private ?string $comment;

    private ?string $after;

    private bool $first;

    /**
     * Constructor.
     */
    public function __construct(
        string $name,
        FieldType $type,
        ?InputType $inputType = null,

        ?int $length = null,
        ?int $precision = null,
        ?int $scale = null,

        bool $nullable = false,
        mixed $default = null,
        bool $unsigned = false,

        bool $unique = false,
        bool $index = false,

        ?string $references = null,
        ?string $on = null,
        ?string $charset = null,
        ?string $collation = null,
        bool|string $constrained = false,
        bool $cascadeOnDelete = false,
        bool $cascadeOnUpdate = false,
        bool $restrictOnDelete = false,
        bool $nullOnDelete = false,

        array $enumValues = [],

        ?string $comment = null,
        ?string $after = null,
        bool $first = false,
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->inputType = $inputType;

        $this->length = $length;
        $this->precision = $precision;
        $this->scale = $scale;

        $this->nullable = $nullable;
        $this->default = $default;
        $this->unsigned = $unsigned;

        $this->unique = $unique;
        $this->index = $index;

        $this->references = $references;
        $this->on = $on;
        $this->charset = $charset;
        $this->collation = $collation;
        $this->constrained = $constrained;
        $this->cascadeOnDelete = $cascadeOnDelete;
        $this->cascadeOnUpdate = $cascadeOnUpdate;
        $this->restrictOnDelete = $restrictOnDelete;
        $this->nullOnDelete = $nullOnDelete;

        $this->enumValues = $enumValues;

        $this->comment = $comment;
        $this->after = $after;
        $this->first = $first;
    }

    /**
     * Construye una instancia desde un arreglo.
     *
     * @param array<string,mixed> $definition
     */
    public static function fromArray(
        array $definition
    ): self {

        self::validateDefinition($definition);

        return new self(

            /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

            name: (string) $definition['name'],

            type: FieldType::from(
                (string) $definition['type']
            ),

            inputType: isset($definition['inputType'])
                ? InputType::from(
                    (string) $definition['inputType']
                )
                : null,

            /*
            |--------------------------------------------------------------------------
            | Definición física
            |--------------------------------------------------------------------------
            */

            length: isset($definition['length'])
                ? (int) $definition['length']
                : null,

            precision: isset($definition['precision'])
                ? (int) $definition['precision']
                : null,

            scale: isset($definition['scale'])
                ? (int) $definition['scale']
                : null,

            nullable: (bool) (
                $definition['nullable'] ?? false
            ),

            default: $definition['default'] ?? null,

            unsigned: (bool) (
                $definition['unsigned'] ?? false
            ),

            /*
            |--------------------------------------------------------------------------
            | Restricciones
            |--------------------------------------------------------------------------
            */

            unique: (bool) (
                $definition['unique'] ?? false
            ),

            index: (bool) (
                $definition['index'] ?? false
            ),

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            references: $definition['references'] ?? null,

            on: $definition['on'] ?? null,

            constrained: $definition['constrained'] ?? false,

            cascadeOnDelete: (bool) (
                $definition['cascadeOnDelete'] ?? false
            ),

            cascadeOnUpdate: (bool) (
                $definition['cascadeOnUpdate'] ?? false
            ),

            restrictOnDelete: (bool) (
                $definition['restrictOnDelete'] ?? false
            ),

            nullOnDelete: (bool) (
                $definition['nullOnDelete'] ?? false
            ),

            /*
            |--------------------------------------------------------------------------
            | Tipos especiales
            |--------------------------------------------------------------------------
            */

            enumValues: $definition['enumValues'] ?? [],

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            comment: $definition['comment'] ?? null,

            after: $definition['after'] ?? null,

            first: (bool) (
                $definition['first'] ?? false
            ),
        );
    }

    /**
     * Valida la definición del campo.
     *
     * @param array<string,mixed> $definition
     */
    private static function validateDefinition(
        array $definition
    ): void {

        if (! isset($definition['name'])) {
            throw new \InvalidArgumentException(
                'The column definition requires the "name" attribute.'
            );
        }

        if (! isset($definition['type'])) {
            throw new \InvalidArgumentException(
                'The column definition requires the "type" attribute.'
            );
        }

        $type = FieldType::from(
            (string) $definition['type']
        );

        /*
        |--------------------------------------------------------------------------
        | ENUM
        |--------------------------------------------------------------------------
        */

        if (
            $type === FieldType::ENUM
            && empty($definition['enumValues'])
        ) {
            throw new \InvalidArgumentException(
                'ENUM fields require the "enumValues" attribute.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Precision / Scale
        |--------------------------------------------------------------------------
        */

        if (isset($definition['precision'])) {

            if ((int) $definition['precision'] <= 0) {

                throw new \InvalidArgumentException(
                    'Precision must be greater than zero.'
                );
            }
        }

        if (
            isset($definition['precision'])
            && isset($definition['scale'])
        ) {

            if (
                (int) $definition['scale']
                >
                (int) $definition['precision']
            ) {

                throw new \InvalidArgumentException(
                    'Scale cannot be greater than precision.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Foreign Key
        |--------------------------------------------------------------------------
        */

        if (
            isset($definition['references'])
            && ! isset($definition['on'])
        ) {

            throw new \InvalidArgumentException(
                'Foreign keys require the "on" attribute.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete rules
        |--------------------------------------------------------------------------
        */

        $deleteRules = 0;

        foreach (
            [
                'cascadeOnDelete',
                'restrictOnDelete',
                'nullOnDelete',
            ] as $rule
        ) {

            if (! empty($definition[$rule])) {
                $deleteRules++;
            }
        }

        if ($deleteRules > 1) {

            throw new \InvalidArgumentException(
                'Only one delete rule may be specified.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Identidad
    |--------------------------------------------------------------------------
    */

    public function name(): string
    {
        return $this->name;
    }

    public function type(): FieldType
    {
        return $this->type;
    }

    public function inputType(): InputType
    {
        return $this->inputType
            ?? $this->type->defaultInputType();
    }

    /*
    |--------------------------------------------------------------------------
    | Definición física
    |--------------------------------------------------------------------------
    */

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

    public function unsigned(): bool
    {
        return $this->unsigned;
    }

    /*
    |--------------------------------------------------------------------------
    | Restricciones
    |--------------------------------------------------------------------------
    */

    public function unique(): bool
    {
        return $this->unique;
    }

    public function index(): bool
    {
        return $this->index;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function references(): ?string
    {
        return $this->references;
    }

    public function on(): ?string
    {
        return $this->on;
    }

    public function constrained(): bool|string
    {
        return $this->constrained;
    }

    public function cascadeOnDelete(): bool
    {
        return $this->cascadeOnDelete;
    }

    public function cascadeOnUpdate(): bool
    {
        return $this->cascadeOnUpdate;
    }

    public function restrictOnDelete(): bool
    {
        return $this->restrictOnDelete;
    }

    public function nullOnDelete(): bool
    {
        return $this->nullOnDelete;
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos especiales
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int,string>
     */
    public function enumValues(): array
    {
        return $this->enumValues;
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    public function comment(): ?string
    {
        return $this->comment;
    }

    public function after(): ?string
    {
        return $this->after;
    }

    public function first(): bool
    {
        return $this->first;
    }

        /*
    |--------------------------------------------------------------------------
    | Helpers - Identificación
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si la columna representa la llave primaria.
     */
    public function isPrimaryKey(): bool
    {
        return match ($this->type) {

            FieldType::ID,
            FieldType::UUID,
            FieldType::ULID => true,

            default => $this->name === 'id',
        };
    }

    /**
     * Determina si corresponde a un timestamp estándar.
     */
    public function isTimestamp(): bool
    {
        return in_array(
            $this->name,
            [
                'created_at',
                'updated_at',
            ],
            true
        );
    }

    /**
     * Determina si representa la columna de SoftDeletes.
     */
    public function isSoftDelete(): bool
    {
        return $this->name === 'deleted_at';
    }

    /**
     * Determina si representa una llave foránea.
     */
    public function isForeignKey(): bool
    {
        return
            $this->type === FieldType::FOREIGN_ID
            || $this->references !== null;
    }

    /**
     * Determina si el campo maneja precisión decimal.
     */
    public function isDecimal(): bool
    {
        return $this->type === FieldType::DECIMAL;
    }

    /**
     * Determina si el campo corresponde a un ENUM.
     */
    public function isEnum(): bool
    {
        return $this->type === FieldType::ENUM;
    }

    /**
     * Determina si el campo corresponde a JSON.
     */
    public function isJson(): bool
    {
        return $this->type === FieldType::JSON;
    }

    /**
     * Determina si el campo se genera automáticamente.
     */
    public function isAutoGenerated(): bool
    {
        return
            $this->isPrimaryKey()
            || $this->isTimestamp()
            || $this->isSoftDelete();
    }

    /**
     * Determina si la columna posee una regla ON DELETE.
     */
    public function hasDeleteRule(): bool
    {
        return
            $this->cascadeOnDelete
            || $this->restrictOnDelete
            || $this->nullOnDelete;
    }

    /**
     * Determina si la columna posee una regla ON UPDATE.
     */
    public function hasUpdateRule(): bool
    {
        return $this->cascadeOnUpdate;
    }

    /**
     * Determina si el campo admite restricciones de llave foránea.
     */
    public function supportsForeignKey(): bool
    {
        return $this->type->supportsForeignKey();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers - Participación en Generadores
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el campo debe ser fillable.
     */
    public function shouldBeFillable(): bool
    {
        return ! $this->isAutoGenerated();
    }

    /**
     * Determina si debe aparecer en formularios.
     */
    public function shouldAppearInForm(): bool
    {
        return ! $this->isAutoGenerated();
    }

    /**
     * Determina si debe aparecer en tablas.
     */
    public function shouldAppearInTable(): bool
    {
        return ! (
            $this->isPrimaryKey()
            || $this->isTimestamp()
            || $this->isSoftDelete()
        );
    }

    /**
     * Determina si debe aparecer en la vista de detalle.
     */
    public function shouldAppearInShow(): bool
    {
        return ! $this->isSoftDelete();
    }

    /**
     * Determina si debe participar en las reglas
     * de validación.
     */
    public function shouldGenerateValidation(): bool
    {
        return ! $this->isAutoGenerated();
    }

    /**
     * Determina si debe incluirse en los Resources API.
     */
    public function shouldAppearInResource(): bool
    {
        return true;
    }

    /**
     * Determina si debe incluirse en la generación
     * automática de filtros.
     */
    public function shouldGenerateFilter(): bool
    {
        return ! (
            $this->isPrimaryKey()
            || $this->isSoftDelete()
        );
    }

    /**
     * Determina si debe permitir ordenamiento.
     */
    public function shouldBeSortable(): bool
    {
        return ! (
            $this->isJson()
            || $this->isSoftDelete()
        );
    }

    /**
     * Determina si debe permitir búsquedas.
     */
    public function shouldBeSearchable(): bool
    {
        return ! (
            $this->isJson()
            || $this->isSoftDelete()
        );
    }

    /**
     * Determina si el campo debe ocultarse
     * automáticamente.
     */
    public function shouldBeHidden(): bool
    {
        return false;
    }

    /**
     * Determina si el campo debe incluirse
     * en exportaciones.
     */
    public function shouldExport(): bool
    {
        return ! $this->isSoftDelete();
    }

    /**
     * Determina si el campo puede editarse
     * después de la creación.
     */
    public function shouldBeEditable(): bool
    {
        return ! $this->isPrimaryKey();
    }

    /**
     * Determina si el campo puede utilizarse
     * en filtros avanzados.
     */
    public function shouldAllowBulkOperations(): bool
    {
        return ! $this->isPrimaryKey();
    }

        /*
    |--------------------------------------------------------------------------
    | Helpers - Estado
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el campo define un InputType explícito.
     */
    public function hasInputType(): bool
    {
        return $this->inputType !== null;
    }

    /**
     * Determina si el campo posee longitud.
     */
    public function hasLength(): bool
    {
        return $this->length !== null;
    }

    /**
     * Determina si el campo posee precisión.
     */
    public function hasPrecision(): bool
    {
        return $this->precision !== null;
    }

    /**
     * Determina si el campo posee escala.
     */
    public function hasScale(): bool
    {
        return $this->scale !== null;
    }

    /**
     * Determina si el campo posee un valor por defecto.
     */
    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    /**
     * Determina si el campo posee comentario.
     */
    public function hasComment(): bool
    {
        return $this->comment !== null;
    }

    /**
     * Determina si el campo posee referencia.
     */
    public function hasReference(): bool
    {
        return $this->references !== null;
    }

    /**
     * Determina si posee tabla relacionada.
     */
    public function hasTableReference(): bool
    {
        return $this->on !== null;
    }

    /**
     * Determina si utiliza constrained().
     */
    public function hasConstraint(): bool
    {
        return $this->constrained !== false;
    }

    /**
     * Determina si posee valores ENUM.
     */
    public function hasEnumValues(): bool
    {
        return $this->enumValues !== [];
    }

    /**
     * Determina si el campo posee metadata "after".
     */
    public function hasAfter(): bool
    {
        return $this->after !== null;
    }

    /**
     * Determina si el campo debe ubicarse primero.
     */
    public function isFirstColumn(): bool
    {
        return $this->first;
    }

    /**
     * Determina si posee alguna restricción.
     */
    public function hasConstraints(): bool
    {
        return
            $this->unique
            || $this->index
            || $this->isForeignKey();
    }

    /**
     * Determina si posee modificadores Blueprint.
     */
    public function hasModifiers(): bool
    {
        return
            $this->nullable
            || $this->unsigned
            || $this->hasDefault()
            || $this->unique
            || $this->index
            || $this->hasComment();
    }

        /*
    |--------------------------------------------------------------------------
    | Helpers - Capacidades
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el tipo soporta índices.
     */
    public function supportsIndex(): bool
    {
        return $this->type->supportsIndex();
    }

    /**
     * Determina si el tipo soporta UNIQUE.
     */
    public function supportsUnique(): bool
    {
        return $this->type->supportsUnique();
    }
        /*
    |--------------------------------------------------------------------------
    | Delegaciones hacia FieldType
    |--------------------------------------------------------------------------
    */

    /**
     * Método Blueprint utilizado por las migraciones.
     */
    public function migrationMethod(): string
    {
        return $this->type->migrationMethod();
    }

    /**
     * Tipo PHP asociado.
     */
    public function phpType(): string
    {
        return $this->type->phpType();
    }

    /**
     * Tipo de input por defecto.
     */
    public function defaultInputType(): InputType
    {
        return $this->type->defaultInputType();
    }

    public function charset(): ?string
    {
        return $this->charset;
    }

    public function collation(): ?string
    {
        return $this->collation;
    }

    public function effectiveLength(): ?int
    {
        return $this->length
            ?? $this->type->defaultLength();
    }

    public function effectivePrecision(): ?int
    {
        return $this->length
            ?? $this->type->defaultLength();
    }

    public function effectiveScale(): ?int
    {
        return $this->length
            ?? $this->type->defaultLength();
    }
}
