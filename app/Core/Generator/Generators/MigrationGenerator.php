<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Processors\MigrationFieldProcessor;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;

/**
 * Genera la migración de un módulo del CN Generator.
 *
 * Su responsabilidad consiste en renderizar la plantilla de migración,
 * escribir el archivo correspondiente y devolver el resultado de la
 * operación.
 */
final class MigrationGenerator extends BaseGenerator
{
    private const STUB = 'migration.stub';

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        private readonly MigrationFieldProcessor $fieldProcessor,
        PresentationFactory $presentationFactory
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        return $this->generateResult(
            self::STUB,
            $module->migrationFile(),
            $this->buildVariables($module)
        );
    }

    /**
     * Construye las variables utilizadas por el stub de migración.
     *
     * @return array<string,string>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

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

        $variables = $module->toStubVariables();

        $variables['columns'] = implode(
            "\n\n",
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
