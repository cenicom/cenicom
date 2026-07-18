<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO\Fields;

use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Enums\InputType;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * ==========================================================================
 * CENICOM ERP
 * ==========================================================================
 *
 * Colección inmutable de ModuleField.
 *
 * Representa el conjunto de campos que pertenecen a un módulo del
 * CN Generator.
 *
 * @implements IteratorAggregate<int, ModuleField>
 */
final class ModuleFields implements IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @var array<int, ModuleField>
     */
    private readonly array $items;

    /**
     * @param array<int, ModuleField> $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $field) {
            if (!$field instanceof ModuleField) {
                throw new \InvalidArgumentException(
                    'Every item must be an instance of ModuleField.'
                );
            }
        }

        $this->items = array_values($items);
    }

    /**
     * @return Traversable<int, ModuleField>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array<int, ModuleField>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function first(): ?ModuleField
    {
        return $this->items[0] ?? null;
    }

    public function last(): ?ModuleField
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->items[array_key_last($this->items)];
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function find(string $name): ?ModuleField
    {
        foreach ($this->items as $field) {
            if ($field->name() === $name) {
                return $field;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public function has(string $name): bool
    {
        return $this->find($name) !== null;
    }

    public function contains(ModuleField $field): bool
    {
        foreach ($this->items as $item) {
            if ($item === $field) {
                return true;
            }
        }

        return false;
    }

    public function firstWhere(callable $callback): ?ModuleField
    {
        foreach ($this->items as $field) {
            if ($callback($field)) {
                return $field;
            }
        }

        return null;
    }

    public function where(callable $callback): self
    {
        return new self(
            array_values(
                array_filter(
                    $this->items,
                    $callback
                )
            )
        );
    }

    public function primaryKey(): ?ModuleField
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isPrimaryKey()
        );
    }

    public function uuid(): ?ModuleField
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isUuid()
        );
    }

    public function firstOfType(FieldType $type): ?ModuleField
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->type() === $type
        );
    }

    public function ofType(FieldType $type): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->type() === $type
        );
    }

    public function firstRelationship(): ?ModuleField
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->hasRelationship()
        );
    }

    public function firstInput(InputType $type): ?ModuleField
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->inputType() === $type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Campos para Migración
    |--------------------------------------------------------------------------
    */
    public function migrationFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldGenerateMigration()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Campos para Modelo
    |--------------------------------------------------------------------------
    */
    public function modelFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldGenerateModel()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Campos para Request
    |--------------------------------------------------------------------------
    */

    public function requestFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldGenerateRequest()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Campos Fillable
    |--------------------------------------------------------------------------
    */
    public function fillableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->fillable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Campos Visibles
    |--------------------------------------------------------------------------
    */
    public function visibleFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->visible()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Campos Ocultos
    |--------------------------------------------------------------------------
    */

    public function hiddenFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->hidden()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 7️⃣ Campos Relacionales
    |--------------------------------------------------------------------------
    */
    public function relationshipFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->hasRelationship()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 8️⃣ Campos Buscables
    |--------------------------------------------------------------------------
    */

    public function searchableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->searchable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 9️⃣ Campos Ordenables
    |--------------------------------------------------------------------------
    */
    public function sortableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->sortable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 🔟 Campos Filtrables
    |--------------------------------------------------------------------------
    */
    public function filterableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->filterable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣1️⃣ Campos Requeridos
    |--------------------------------------------------------------------------
    */

    public function requiredFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->isRequired()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣2️⃣ Campos Nullable
    |--------------------------------------------------------------------------
    */
    public function nullableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->isNullable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣3️⃣ Campos Únicos
    |--------------------------------------------------------------------------
    */
    public function uniqueFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->isUniqueRule()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣4️⃣ Campos de Auditoría
    |--------------------------------------------------------------------------
    */
    public function auditFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->isAuditField()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣5️⃣ Campos Exportables
    |--------------------------------------------------------------------------
    */
    public function exportableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldExport()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣6️⃣ Campos Renderizables
    |--------------------------------------------------------------------------
    */
    public function formFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldRenderInForm()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣7️⃣ Columnas de Tabla
    |--------------------------------------------------------------------------
    */
    public function tableFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldRenderInTable()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣8️⃣ Campos API
    |--------------------------------------------------------------------------
    */
    public function apiFields(): self
    {
        return $this->where(
            fn(ModuleField $field) => $field->shouldAppearInApi()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ ¿Tiene relaciones?
    |--------------------------------------------------------------------------
    */
    public function hasRelationships(): bool
    {
        return $this->relationshipFields()->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ ¿Tiene UUID?
    |--------------------------------------------------------------------------
    */
    public function hasUuid(): bool
    {
        return $this->uuid() !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ ¿Tiene Soft Deletes?
    |--------------------------------------------------------------------------
    */



    public function hasSoftDeletes(): bool
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isSoftDeleteColumn()
        ) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ ¿Tiene Timestamps?
    |--------------------------------------------------------------------------
    */
    public function hasTimestamps(): bool
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isTimestampColumn()
        ) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ ¿Tiene archivos?
    |--------------------------------------------------------------------------
    */
    public function hasFiles(): bool
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isFile()
        ) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ ¿Tiene imágenes?
    |--------------------------------------------------------------------------
    */
    public function hasImages(): bool
    {
        return $this->firstWhere(
            fn(ModuleField $field) => $field->isImage()
        ) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 7️⃣ ¿Tiene campos buscables?
    |--------------------------------------------------------------------------
    */
    public function hasSearchableFields(): bool
    {
        return $this->searchableFields()->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | 8️⃣ ¿Tiene campos ordenables?
    |--------------------------------------------------------------------------
    */
    public function hasSortableFields(): bool
    {
        return $this->sortableFields()->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | 9️⃣ ¿Tiene filtros?
    |--------------------------------------------------------------------------
    */
    public function hasFilterableFields(): bool
    {
        return $this->filterableFields()->isNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | 🔟 Cantidad de columnas
    |--------------------------------------------------------------------------
    */
    public function databaseColumnCount(): int
    {
        return $this->migrationFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣1️⃣ Cantidad de relaciones
    |--------------------------------------------------------------------------
    */
    public function relationshipCount(): int
    {
        return $this->relationshipFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣2️⃣ Cantidad de campos visibles
    |--------------------------------------------------------------------------
    */
    public function visibleCount(): int
    {
        return $this->visibleFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣3️⃣ Cantidad de campos ocultos
    |--------------------------------------------------------------------------
    */
    public function hiddenCount(): int
    {
        return $this->hiddenFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣4️⃣ Cantidad de campos requeridos
    |--------------------------------------------------------------------------
    */
    public function requiredCount(): int
    {
        return $this->requiredFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣5️⃣ Cantidad de campos fillable
    |--------------------------------------------------------------------------
    */
    public function fillableCount(): int
    {
        return $this->fillableFields()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣6️⃣ Cantidad por tipo
    |--------------------------------------------------------------------------
    */
    public function countByType(FieldType $type): int
    {
        return $this->ofType($type)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣7️⃣ Resumen estadístico
    |--------------------------------------------------------------------------
    */
    public function statistics(): array
    {
        return [
            'total' => $this->count(),
            'database_columns' => $this->databaseColumnCount(),
            'relationships' => $this->relationshipCount(),
            'fillable' => $this->fillableCount(),
            'required' => $this->requiredCount(),
            'visible' => $this->visibleCount(),
            'hidden' => $this->hiddenCount(),
            'has_uuid' => $this->hasUuid(),
            'has_soft_deletes' => $this->hasSoftDeletes(),
            'has_timestamps' => $this->hasTimestamps(),
            'has_relationships' => $this->hasRelationships(),
            'has_files' => $this->hasFiles(),
            'has_images' => $this->hasImages(),
            'has_searchable' => $this->hasSearchableFields(),
            'has_sortable' => $this->hasSortableFields(),
            'has_filterable' => $this->hasFilterableFields(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Transformations
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Conversión a Array
    |--------------------------------------------------------------------------
    */
    /**
     * @return array<int, ModuleField>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /*
    |--------------------------------------------------------------------------
    |
    | 2️⃣ Array indexado por nombre
    /        Muy útil para búsquedas rápidas y serialización.
    /--------------------------------------------------------------------------
    */
    /**
     * @return array<string, ModuleField>
     */
    public function toIndexedArray(): array
    {
        $result = [];

        foreach ($this->items as $field) {
            $result[$field->name()] = $field;
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Mapa Nombre → Campo
    |--------------------------------------------------------------------------
    */
    /**
     * @return array<string, ModuleField>
     */
    public function toNameMap(): array
    {
        return $this->toIndexedArray();
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Lista de nombres
    |--------------------------------------------------------------------------
    */
    /**
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_map(
            static fn(ModuleField $field): string => $field->name(),
            $this->items
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Lista de etiquetas
    |--------------------------------------------------------------------------
    */
    /**
     * @return array<int, string>
     */
    public function labels(): array
    {
        return array_map(
            static fn(ModuleField $field): string => $field->label(),
            $this->items
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Pluck genérico
    |--------------------------------------------------------------------------
    */
    /**
     * @template T
     *
     * @param callable(ModuleField):T $callback
     * @return array<int,T>
     */
    public function pluck(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /*
    |--------------------------------------------------------------------------
    | 7️⃣ Map
    |--------------------------------------------------------------------------
    */
    /**
     * @template T
     *
     * @param callable(ModuleField):T $callback
     * @return array<int,T>
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /*
    |--------------------------------------------------------------------------
    | 8️⃣ Cada elemento
    |--------------------------------------------------------------------------
    */
    public function each(callable $callback): void
    {
        foreach ($this->items as $field) {
            $callback($field);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 9️⃣ Exportación JSON
    |--------------------------------------------------------------------------
    */
    public function toJson(
        int $flags = JSON_PRETTY_PRINT
    ): string {
        return json_encode(
            $this,
            $flags | JSON_THROW_ON_ERROR
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 🔟 Serialización
        Ya implementamos:
    |--------------------------------------------------------------------------

    public function jsonSerialize(): array
    {
        return $this->items;
    }*/

    /*
    |--------------------------------------------------------------------------
    | 1️⃣1️⃣ Conversión mediante callback
        Una utilidad muy poderosa para futuros Generators.
    |--------------------------------------------------------------------------
    */
    /**
     * @template T
     *
     * @param callable(ModuleField):T $callback
     * @return array<int,T>
     */
    public function transform(callable $callback): array
    {
        return array_map($callback, $this->items);
    }


}










