<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\ManifestLoader;
use Illuminate\Console\Command;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Comando oficial del CN Generator.
 *
 * Punto de entrada para la generación automática
 * de módulos del ERP.
 *
 * Su responsabilidad consiste únicamente en:
 *
 * - Leer argumentos y opciones.
 * - Construir el ModuleData.
 * - Ejecutar ModuleGenerator.
 * - Mostrar el resultado.
 *
 * Toda la lógica de generación permanece encapsulada
 * dentro del núcleo del CN Generator.
 */
final class GenerateModuleCommand extends Command
{
    /**
     * Nombre del comando.
     */
    protected $signature = 'cn:generate
                            {module : Nombre del módulo}';

    /**
     * Descripción.
     */
    protected $description = 'Generate a complete CENICOM ERP module.';

    public function __construct(
        private readonly ModuleDataFactory $factory,
        private readonly ModuleGenerator $generator,
        private readonly ManifestLoader $manifestLoader,
    ) {
        parent::__construct();
    }

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        $this->showHeader();

        $module = $this->buildModuleData();

        $this->showModuleInfo($module);

        $result = $this->generator->generate($module);

        $this->displayResult($result);

        return $this->exitCode($result);
    }

    private function showHeader(): void
    {
        $this->newLine();

        $this->info('===========================================');
        $this->info('        CENICOM ERP - CN Generator');
        $this->info('===========================================');

        $this->newLine();
    }

    /**
     * Construye el ModuleData.
     */
    private function buildModuleData(): ModuleData
    {
        $definition = $this->manifestLoader->load(
            $this->argument('module')
        );

        return $this->factory->create($definition);
    }

    private function showModuleInfo(
        ModuleData $module
    ): void {

        $this->line('Module      : ' . $module->name());

        $this->line('Table       : ' . $module->table());

        $this->line('Namespace   : ' . $module->modelNamespace());

        $this->line('Route       : ' . $module->routePrefix());

        $this->newLine();
    }

    /**
     * Presenta el resultado completo.
     */
    private function displayResult(
        GeneratorResult $result
    ): void {
        foreach ($result->warnings() as $warning) {
            $this->warn($warning);
        }

        foreach ($result->errors() as $error) {
            $this->error($error);
        }

        $this->newLine();

        $this->table(
            ['Operation', 'Count'],
            [
                ['Created', $result->createdCount()],
                ['Updated', $result->updatedCount()],
                ['Skipped', $result->skippedCount()],
                ['Warnings', $result->warningCount()],
                ['Errors', $result->errorCount()],
            ]
        );
    }

    private function exitCode(
        GeneratorResult $result
    ): int {

        return $result->hasErrors()
            ? self::FAILURE
            : self::SUCCESS;
    }

    /**
     * Muestra advertencias.
     */
    private function displayWarnings(
        GeneratorResult $result
    ): void {}

    /**
     * Muestra errores.
     */
    private function displayErrors(
        GeneratorResult $result
    ): void {}

    /**
     * Muestra el resumen final.
     */
    private function displaySummary(
        GeneratorResult $result
    ): void {}
}
