<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * Clase base para los generadores del CN Generator.
 *
 * Proporciona la infraestructura común para todos los generadores
 * especializados, manteniendo una arquitectura desacoplada y
 * preparada para futuras extensiones.
 *
 * Esta clase no contiene lógica específica de generación; dicha
 * responsabilidad corresponde a las implementaciones concretas.
 */
abstract class BaseGenerator implements GeneratorInterface
{
    /**
     * Crea una nueva instancia del generador base.
     */
    public function __construct(
        protected StubManager $stubManager,
        protected FileWriter $fileWriter,
        protected PresentationFactory $presentationFactory,
        protected GeneratorValidator $validator,
    ) {}

    /**
     * {@inheritDoc}
     */
    abstract public function supports(ModuleData $module): bool;

    /**
     * {@inheritDoc}
     */
    abstract public function generate(ModuleData $module): GeneratorResult;

    /**
     * Variables universales disponibles para cualquier stub.
     *
     *  @param array<string,mixed> $variables
     */
    protected function render(
        string $stub,
        array $variables
    ): string {

        return $this->stubManager->render(
            $stub,
            $variables
        );
    }

    protected function write(
        string $path,
        string $content
    ): void {
        $this->fileWriter->write(
            $path,
            $content
        );
    }

    protected function generateResult(
        string $stub,
        string $path,
        array $variables
    ): GeneratorResult {

        $result = new GeneratorResult();

        if ($this->fileWriter->exists($path)) {
            return $result->addSkipped($path);
        }

        $this->ensureStubExists($stub);

        $this->validateVariables($variables);

        $content = $this->render(
            $stub,
            $variables
        );

        $this->fileWriter->write(
            $path,
            $content
        );

        $this->validateGeneratedFile($path);

        return $result->addCreated($path);
    }

    /**
     * Variables base disponibles para todos los stubs.
     *
     * Estas variables representan información común del módulo
     * utilizada por múltiples generadores.
     *
     * @return array<string,mixed>
     */
    protected function defaultVariables(
        ModuleData $module
    ): array {

        return array_merge(
            $module->toStubVariables(),
            $this->buildPresentationVariables($module),
            [

                /*
                |--------------------------------------------------------------------------
                | Variables dinámicas de nombres
                |--------------------------------------------------------------------------
                */

                'variable' => $module->variable(),

                'pluralVariable' => $module->pluralVariable(),

                /*
                |--------------------------------------------------------------------------
                | Modelo
                |--------------------------------------------------------------------------
                */

                'modelClass' => $module->modelClass(),

                'qualifiedModel' => $module->qualifiedModel(),


                /*
                |--------------------------------------------------------------------------
                | Rutas
                |--------------------------------------------------------------------------
                */

                'routeResource' => $module->routeResource(),

                'routeIndex' => $module->routeIndex(),

                'routeCreate' => $module->routeCreate(),

                'routeEdit' => $module->routeEdit(),

                'routeStore' => $module->routeStore(),

                'routeUpdate' => $module->routeUpdate(),

                'routeDestroy' => $module->routeDestroy(),


                /*
                |--------------------------------------------------------------------------
                | Vistas
                |--------------------------------------------------------------------------
                */

                'collection' => $module->pluralVariable(),

                'indexView' => $module->indexView(),

                'createView' => $module->createView(),

                'editView' => $module->editView(),

                'showView' => $module->showView(),

            ]
        );
    }

    /**
     * Construye variables relacionadas con la presentación.
     *
     * @return array<string,mixed>
     */
    protected function buildPresentationVariables(
        ModuleData $module,
    ): array {

        $form = $this->presentationFactory->form($module);

        $table = $this->presentationFactory->table($module);

        return [

            'form_fields'   => $form->fields(),
            'form_rows'     => $form->rows(),
            'form_sections' => $form->sections(),

            'table_columns' => $table->columns(),

        ];
    }

    /**
     * Verifica que exista el stub solicitado.
     */
    protected function ensureStubExists(string $stub): void
    {
        $this->stubManager->ensureExists($stub);
    }

    /**
     * Valida las variables enviadas al stub.
     *
     * @param array<string,mixed> $variables
     */
    protected function validateVariables(array $variables): void
    {
        foreach ($variables as $key => $value) {

            if ($value instanceof \Closure) {

                throw new \RuntimeException(
                    "La variable '{$key}' no puede ser Closure."
                );
            }
        }
    }

    /**
     * Valida el archivo generado.
     */
    protected function validateGeneratedFile(
        string $path
    ): void {

        if (! is_file($path)) {
            throw new \RuntimeException(
                "No fue posible generar {$path}."
            );
        }

        if (! is_readable($path)) {
            throw new \RuntimeException(
                "No fue posible leer {$path}."
            );
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException(
                "No fue posible leer {$path}."
            );
        }

        if (trim($content) === '') {
            throw new \RuntimeException(
                "El archivo {$path} quedó vacío."
            );
        }

        if (
            str_contains($content, '[[') ||
            str_contains($content, ']]')
        ) {
            throw new \RuntimeException(
                "El archivo {$path} contiene placeholders sin reemplazar."
            );
        }
    }
}
