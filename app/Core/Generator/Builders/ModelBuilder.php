<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use RuntimeException;

final class ModelBuilder
{
    /**
     * @return array<string,mixed>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->modelNamespace(),

            'model' => $module->modelClass(),

            'description' => $module->description(),

            'table' => $module->table(),

            'fillable' => $this->buildFillable($module),

            'casts' => $this->buildCasts($module),

            'imports' => $this->buildImports($module),

            'traits' => $this->buildTraits($module),

            'constants' => $this->buildConstants($module),

            'relationships' => $this->buildRelationships($module),

            'scopes' => $this->buildScopes($module),
        ];
    }

    /**
     * Construye el contenido de la propiedad $fillable.
     */
    private function buildFillable(ModuleData $module): string
    {
        $fillable = [];

        foreach ($module->columns() as $column) {

            if (!$column->shouldBeFillable()) {
                continue;
            }

            $fillable[] = sprintf(
                "        '%s',",
                $column->name()
            );
        }

        return implode(
            PHP_EOL,
            $fillable
        );
    }

    private function buildCasts(ModuleData $module): string
    {
        $casts = [];

        foreach ($module->columns() as $column) {

            $cast = $this->resolveCast($column);

            if ($cast !== null) {
                $casts[] = sprintf(
                    "        '%s' => '%s',",
                    $column->name(),
                    $cast
                );
            }
        }

        return implode(
            PHP_EOL,
            $casts
        );
    }

    private function resolveCast(
        ColumnDefinition $column
    ): ?string {
        return $column->type()->cast();
    }

    /**
     * Construye el bloque de imports del modelo.
     */
    private function buildImports(ModuleData $module): string
    {
        $imports = array_unique(
            $this->resolveImports($module)
        );

        sort($imports);

        return implode(
            PHP_EOL,
            array_map(
                static fn(string $import): string => sprintf(
                    'use %s;',
                    $import
                ),
                $imports
            )
        );
    }

    /**
     * @return array<int,string>
     */
    private function resolveImports(ModuleData $module): array
    {
        $imports = [
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            'Illuminate\Database\Eloquent\Model',
        ];

        if ($module->softDeletes()) {
            $imports[] =
                'Illuminate\Database\Eloquent\SoftDeletes';
        }

        if ($module->uuid()) {
            $imports[] =
                'Illuminate\Database\Eloquent\Concerns\HasUuids';
        }

        if (!empty($this->resolveRelationships($module))) {

            $imports[] =
                'Illuminate\Database\Eloquent\Relations\BelongsTo';

            $imports[] =
                'Illuminate\Database\Eloquent\Relations\HasOne';

            $imports[] =
                'Illuminate\Database\Eloquent\Relations\HasMany';

            $imports[] =
                'Illuminate\Database\Eloquent\Relations\BelongsToMany';

            $imports = array_merge(
                $imports,
                $this->resolveRelationshipImports($module)
            );
        }

        if (!empty($this->resolveScopes($module))) {
            $imports[] =
                'Illuminate\Database\Eloquent\Builder';
        }

        return $imports;
    }

    private function buildTraits(ModuleData $module): string
    {
        $traits = array_unique(
            $this->resolveTraits($module)
        );

        sort($traits);

        return implode(
            PHP_EOL,
            array_map(
                static fn(string $trait): string =>
                    "    use {$trait};",
                $traits
            )
        );
    }

    private function resolveTraits(ModuleData $module): array
    {
        $traits = [
            'HasFactory',
        ];

        if ($module->softDeletes()) {
            $traits[] = 'SoftDeletes';
        }

        if ($module->uuid()) {
            $traits[] = 'HasUuids';
        }

        return $traits;
    }

    private function resolveConstants(
        ModuleData $module
    ): array {
        return [];
    }

    private function buildConstants(
        ModuleData $module
    ): string {
        $constants = $this->resolveConstants($module);

        return implode(
            PHP_EOL . PHP_EOL,
            array_map(
                static fn(array $constant): string => sprintf(
                    '    public const %s = %s;',
                    $constant['name'],
                    $constant['value']
                ),
                $constants
            )
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function resolveRelationships(
        ModuleData $module
    ): array {
        if (!method_exists($module, 'relationships')) {
            return [];
        }

        return $module->relationships();
    }

    /**
     * @param array<string,mixed> $relationship
     */
    private function buildRelationship(
        array $relationship
    ): string {
        return match ($relationship['type']) {

            'belongsTo' =>
                $this->buildBelongsTo($relationship),

            'hasOne' =>
                $this->buildHasOne($relationship),

            'hasMany' =>
                $this->buildHasMany($relationship),

            'belongsToMany' =>
                $this->buildBelongsToMany($relationship),

            default => throw new RuntimeException(
                sprintf(
                    'Unsupported relationship type [%s].',
                    $relationship['type'] ?? 'unknown',
                ),
            ),
        };
    }

    /**
     * @param array<string,mixed> $relationship
     */
    private function buildBelongsTo(
        array $relationship
    ): string {
        return sprintf(
            <<<'PHP'
    public function %s(): BelongsTo
    {
        return $this->belongsTo(%s::class);
    }
PHP,
            $relationship['method'],
            $relationship['model'],
        );
    }

    /**
     * @param array<string,mixed> $relationship
     */
    private function buildHasOne(
        array $relationship
    ): string {
        return sprintf(
            <<<'PHP'
    public function %s(): HasOne
    {
        return $this->hasOne(%s::class);
    }
PHP,
            $relationship['method'],
            $relationship['model'],
        );
    }

    /**
     * @param array<string,mixed> $relationship
     */
    private function buildHasMany(
        array $relationship
    ): string {
        return sprintf(
            <<<'PHP'
    public function %s(): HasMany
    {
        return $this->hasMany(%s::class);
    }
PHP,
            $relationship['method'],
            $relationship['model'],
        );
    }

    /**
     * @param array<string,mixed> $relationship
     */
    private function buildBelongsToMany(
        array $relationship
    ): string {
        return sprintf(
            <<<'PHP'
    public function %s(): BelongsToMany
    {
        return $this->belongsToMany(%s::class);
    }
PHP,
            $relationship['method'],
            $relationship['model'],
        );
    }

    private function buildRelationships(
        ModuleData $module
    ): string {
        $relationships = array_map(
            fn(array $relationship): string =>
                $this->buildRelationship($relationship),
            $this->resolveRelationships($module),
        );

        return implode(
            PHP_EOL . PHP_EOL,
            array_filter($relationships),
        );
    }

    private function buildScopes(
        ModuleData $module
    ): string {
        $scopes = array_map(
            fn(array $scope): string =>
                $this->buildScope($scope),
            $this->resolveScopes($module)
        );

        return implode(
            PHP_EOL . PHP_EOL,
            array_filter($scopes)
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function resolveScopes(
        ModuleData $module
    ): array {
        if (!method_exists($module, 'scopes')) {
            return [];
        }

        return $module->scopes();
    }

    /**
     * @param array<string,mixed> $scope
     */
    private function buildScope(
        array $scope
    ): string {
        return sprintf(
            <<<'PHP'
    public function scope%s(Builder $query): Builder
    {
        return $query->%s;
    }
PHP,
            ucfirst($scope['name']),
            $scope['body'],
        );
    }

    /**
     * @return array<int,string>
     */
    private function resolveRelationshipImports(
        ModuleData $module
    ): array {
        if (!method_exists($module, 'relationships')) {
            return [];
        }

        $imports = [];

        foreach ($module->relationships() as $relationship) {

            if (
                !isset($relationship['model'])
                || empty($relationship['model'])
            ) {
                continue;
            }

            $imports[] = $relationship['model'];
        }

        return $imports;
    }
}
