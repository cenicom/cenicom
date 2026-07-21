<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Presentation\Factory\PresentationFactory;

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
        protected PresentationFactory $presentationFactory
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

    /**
     * Genera un archivo a partir de un stub.
     *
     * Centraliza el flujo de renderizado y escritura para
     * todos los generadores del CN Generator.
     *
     * @param string $stub Nombre del stub.
     * @param string $path Ruta destino.
     * @param array<string,mixed> $variables Variables del stub.
     */
    protected function generateFile(
        string $stub,
        string $path,
        array $variables
    ): bool {

        if ($this->fileWriter->exists($path)) {
            return false;
        }

        $content = $this->render(
            $stub,
            $variables
        );

        $this->write(
            $path,
            $content
        );

        return true;
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

        if ($this->generateFile($stub, $path, $variables)) {
            $result->addCreated($path);
        } else {
            $result->addSkipped($path);
        }

        return $result;
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
        ModuleData $module
    ): array {

        $form = $this->presentationFactory
            ->form($module)
            ->metadata();

        $table = $this->presentationFactory
            ->table($module)
            ->metadata();

        return [

            /*
        |--------------------------------------------------------------------------
        | Formulario
        |--------------------------------------------------------------------------
        */

            'form_fields' => $form['fields'],

            'form_rows' => $form['rows'],

            'form_sections' => $form['sections'],


            /*
        |--------------------------------------------------------------------------
        | Tabla
        |--------------------------------------------------------------------------
        */

            'table_columns' => $table['columns'],

            'table_labels' => $table['labels'],

            'table_names' => $table['names'],

        ];
    }
}
