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
use App\Core\Generator\Validation\GeneratorValidator;

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
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly MigrationFieldProcessor $fieldProcessor,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ModuleData $module): bool
    {
        return $module->table() !== '';
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

        $columns = array_filter([
            $module->uuid()
                ? "\$table->uuid('id')->primary();"
                : null,

            $this->fieldProcessor->process(
                $module->columns()
            ),
        ]);

        if ($module->uuid()) {
            $columns[] = "\$table->uuid('id')->primary();";
        }

        $fieldColumns = $this->fieldProcessor->process(
            $module->columns()
        );

        if ($fieldColumns !== '') {
            $columns[] = $fieldColumns;
        }

        $variables = $this->defaultVariables($module);

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
