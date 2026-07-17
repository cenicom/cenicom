<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
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
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
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
    public function generate(ModuleData $module): GeneratorResult
    {
        $columns = $this->fieldProcessor->process(
            $module->columns()
        );

        $variables = $module->toStubVariables();

        $variables['columns'] = $columns;

        $variables['timestamps'] = $module->timestamps()
            ? '$table->timestamps();'
            : '';

        $variables['softDeletes'] = $module->softDeletes()
            ? '$table->softDeletes();'
            : '';

        $file = $module->migrationFile();

        $result = new GeneratorResult();

        if (
            $this->generateFile(
                self::STUB,
                $file,
                $variables
            )
        ) {
            $result->addCreated($file);
        } else {
            $result->addSkipped($file);
        }

        return $result;
    }




}
