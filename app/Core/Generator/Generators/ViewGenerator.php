<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Processors\FormFieldProcessor;
use App\Core\Generator\Processors\ViewFieldProcessor;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente las vistas Blade de un módulo.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ViewGenerator extends BaseGenerator
{
    private const VIEWS = [

        'views/index.stub' => 'index.blade.php',

        'views/create.stub' => 'create.blade.php',

        'views/edit.stub' => 'edit.blade.php',

        'views/show.stub' => 'show.blade.php',

        'views/_form.stub' => '_form.blade.php',

    ];

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        private readonly FormFieldProcessor $formFieldProcessor,
        private readonly ViewFieldProcessor $viewFieldProcessor,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
        );
    }

    public function supports(
        ModuleData $module
    ): bool {
        return true;
    }

    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();

        foreach (self::VIEWS as $stub => $filename) {

            $path = $module->viewPath()
                . DIRECTORY_SEPARATOR
                . $filename;

            $generated = $this->generateFile(
                $stub,
                $path,
                $this->buildVariables($module)
            );

            $result->addGenerated(
                $path,
                $generated
            );

        }

        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        $formFields = $this->formFieldProcessor->process(
            $module->columns(),
            $module->variable(),
        );

        return [

            'title' => $module->plural(),

            'description' => $module->description(),

            'model' => lcfirst(
                $module->modelClass()
            ),

            'modelClass' => $module->modelClass(),

            'singular' => $module->singular(),

            'plural' => $module->plural(),

            'collection' => $this->camelPlural(
                $module->plural()
            ),

            'routePrefix' => $module->routePrefix(),

            'routeName' => $module->routeName(),

            'viewPrefix' => $module->viewPrefix(),

            'table' => $module->table(),

            'fields' => $module->columns(),

            'columns' => $this->viewFieldProcessor->renderShow(
                $module->columns(),
                $module->variable()
            ),

            'columnCount' => count($module->columns()) + 1,

            'form_fields' => $formFields,


        ];
    }

    private function camelPlural(
        string $value
    ): string {

        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords($value)
            )
        );
    }
}
