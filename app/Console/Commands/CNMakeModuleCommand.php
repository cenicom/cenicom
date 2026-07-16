<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Factories\ModuleDefinitionFactory;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Specifications\ModuleSpecification;
use Illuminate\Console\Command;
use Throwable;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Comando oficial del CN Generator Framework.
 *
 * Genera automáticamente un módulo completo del ERP
 * utilizando la infraestructura Enterprise del
 * CN Generator.
 *
 * @package App\Console\Commands
 * @since 1.0.0
 */
final class CNMakeModuleCommand extends Command
{
    /**
     * Nombre y firma del comando.
     *
     * @var string
     */
    protected $signature = '
        cn:make-module
        {name : Module name}
        {--force : Overwrite existing files}
    ';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Generate a complete CN module.';

    public function __construct(
        private readonly ModuleDefinitionFactory $definitionFactory,
        private readonly ModuleDataFactory $moduleDataFactory,
        private readonly ModuleGenerator $moduleGenerator,
    ) {
        parent::__construct();
    }

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        try {

            $specification = new ModuleSpecification(
                $this->argument('name')
            );

            $definition = $this->definitionFactory->create(
                $specification
            );

            $module = $this->moduleDataFactory->create(
                $definition
            );

            $result = $this->moduleGenerator->generate(
                $module
            );

            $this->displaySummary(
                $result,
                $module->name()
            );

            return $result->hasErrors()
                ? self::FAILURE
                : self::SUCCESS;

        } catch (Throwable $exception) {

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    /**
     * Muestra el resumen de la generación.
     */
    private function displaySummary(
        GeneratorResult $result,
        string $module
    ): void {

        $summary = $result->summary();

        $this->newLine();

        $this->info('======================================');
        $this->info('      CN Generator Framework');
        $this->info('======================================');

        $this->table(
            ['Operation', 'Count'],
            [
                ['Created', $summary['created']],
                ['Updated', $summary['updated']],
                ['Skipped', $summary['skipped']],
                ['Warnings', $summary['warnings']],
                ['Errors', $summary['errors']],
            ]
        );

        if ($result->hasErrors()) {

            $this->error(
                "Module {$module} generated with errors."
            );

            foreach ($result->errors() as $error) {
                $this->line(" - {$error}");
            }

            return;
        }

        if ($result->hasWarnings()) {

            foreach ($result->warnings() as $warning) {
                $this->warn($warning);
            }
        }

        $this->info(
            "Module {$module} generated successfully."
        );

        $this->newLine();
    }
}
