<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Request;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Enums\FieldType;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * los Form Requests del módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir nombre de clase.
 * - Construir reglas Laravel.
 * - Resolver reglas por tipo.
 * - Resolver claves foráneas.
 * - Resolver únicos.
 *
 * RequestGenerator únicamente orquesta el proceso.
 */
final class RequestBuilder
{
    /**
     * Punto de entrada.
     *
     * @return array<string,string>
     */
    public function build(
        ModuleData $module,
    ): array {

        return $this->buildVariables(
            $module,
        );
    }


    /**
     * Construye variables del stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(
        ModuleData $module,
    ): array {

        return [

            'namespace' => $module->requestNamespace(),

            'singular'
            => $module->singular(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

            'rules' => $this->buildRules($module),

        ];
    }

    /**
     * Determina si una columna debe generar regla.
     */
    private function shouldGenerateRule(
        ColumnDefinition $column
    ): bool {

        return $column->shouldAppearInForm();
    }

    /**
     * Construye las reglas Laravel del Request.
     */
    private function buildRules(
        ModuleData $module
    ): string {

        $rules = [];

        foreach ($module->columns() as $column) {

            if (! $this->shouldGenerateRule($column)) {
                continue;
            }

            $rules[] = sprintf(
                "            '%s' => %s,",
                $column->name(),
                $this->buildRule(
                    $column,
                    $module
                )
            );
        }

        return implode(
            PHP_EOL,
            $rules
        );
    }

    /**
     * Construye una regla completa para una columna.
     */
    private function buildRule(
        ColumnDefinition $column,
        ModuleData $module
    ): string {

        $rules = [];

        $rules[] = $this->resolveRequiredRule($column);

        $rules = array_merge(
            $rules,
            $this->resolveTypeRules($column)
        );

        if ($length = $this->resolveLengthRule($column)) {
            $rules[] = $length;
        }

        if ($foreign = $this->resolveForeignRule($column)) {
            $rules[] = $foreign;
        }

        if ($unique = $this->resolveUniqueRule($column, $module)) {
            $rules[] = $unique;
        }

        return "['" . implode("', '", $rules) . "']";
    }

    private function resolveRequiredRule(
        ColumnDefinition $column
    ): string {

        return $column->nullable()
            ? 'nullable'
            : 'required';
    }

    private function resolveLengthRule(
        ColumnDefinition $column
    ): ?string {

        return $column->length() !== null
            ? 'max:' . $column->length()
            : null;
    }

    private function resolveTypeRules(
        ColumnDefinition $column
    ): array {

        return match ($column->type()) {

            FieldType::STRING => ['string'],

            FieldType::TEXT => ['string'],

            FieldType::INTEGER => ['integer'],

            FieldType::DECIMAL => ['numeric'],

            FieldType::BOOLEAN => ['boolean'],

            FieldType::DATE => ['date'],

            FieldType::DATETIME => ['date'],

            FieldType::UUID => ['uuid'],

            FieldType::EMAIL => ['email'],

            FieldType::BIG_INTEGER => ['integer'],

            FieldType::FLOAT => ['float'],

            FieldType::DOUBLE => ['double'],

            FieldType::JSON => ['json'],

            default => ['string'],
        };
    }

    private function resolveForeignRule(
        ColumnDefinition $column
    ): ?string {

        if (!$column->isForeignKey()) {
            return null;
        }

        return sprintf(
            'exists:%s,%s',
            $column->on(),
            $column->references()
        );
    }

    /**
     * Resuelve la regla unique de Laravel.
     */
    private function resolveUniqueRule(
        ColumnDefinition $column,
        ModuleData $module
    ): ?string {

        if (! $column->unique()) {
            return null;
        }

        return sprintf(
            'unique:%s,%s',
            $module->table(),
            $column->name()
        );
    }
}
