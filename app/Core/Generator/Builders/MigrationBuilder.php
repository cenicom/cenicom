<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Processors\MigrationFieldProcessor;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * la migración de un módulo.
 *
 * Responsabilidades:
 *
 * - Construir las columnas Blueprint.
 * - Construir timestamps.
 * - Construir soft deletes.
 * - Preparar variables para el StubManager.
 *
 * La transformación de ColumnDefinition a Blueprint
 * permanece delegada a MigrationFieldProcessor.
 */
final class MigrationBuilder
{
    public function __construct(
        private readonly MigrationFieldProcessor $fieldProcessor,
    ) {}

    /**
     * Punto de entrada.
     *
     * @return array<string,mixed>
     */
    public function build(ModuleData $module): array
    {
        $variables = $module->toStubVariables();

        $columns = [];

        if ($module->uuid()) {
            $columns[] = "\$table->uuid('id')->primary();";
        }

        $fieldColumns = $this->fieldProcessor->process(
            $module->columns()
        );

        if ($fieldColumns !== '') {
            $columns[] = $fieldColumns;
        }

        $variables['columns'] = implode(
            PHP_EOL,
            $columns
        );

        $variables['timestamps'] = $module->timestamps()
            ? '$table->timestamps();'
            : '';

        $variables['softDeletes'] = $module->softDeletes()
            ? '$table->softDeletes();'
            : '';

        return $variables;
    }
}
