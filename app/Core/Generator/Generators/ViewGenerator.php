<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente las vistas Blade de un módulo.
 *
 * Procesa los stubs correspondientes utilizando la información
 * contenida en ModuleData y persiste los resultados mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ViewGenerator extends BaseGenerator
{
    private const INDEX_STUB = 'views/index.stub';
    private const CREATE_STUB = 'views/create.stub';
    private const EDIT_STUB = 'views/edit.stub';
    private const SHOW_STUB = 'views/show.stub';
    private const FORM_STUB = 'views/_form.stub';
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera todas las vistas del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();

        foreach ([
            $this->generateIndex($module),
            $this->generateCreate($module),
            $this->generateEdit($module),
            $this->generateShow($module),
            $this->generateForm($module),
        ] as $file) {

            $result->addCreated($file);
        }

        return $result;
    }

    /**
     * Genera index.blade.php
     */
    private function generateIndex(
        ModuleData $module
    ): string {

        return $this->writeView(
            $module,
            self::INDEX_STUB,
            'index.blade.php'
        );
    }

    /**
     * Genera create.blade.php
     */
    private function generateCreate(
        ModuleData $module
    ): string {

        return $this->writeView(
            $module,
            self::CREATE_STUB,
            'create.blade.php'
        );
    }

    /**
     * Genera edit.blade.php
     */
    private function generateEdit(
        ModuleData $module
    ): string {

        return $this->writeView(
            $module,
            self::EDIT_STUB,
            'edit.blade.php'
        );
    }

    /**
     * Genera show.blade.php
     */
    private function generateShow(
        ModuleData $module
    ): string {

        return $this->writeView(
            $module,
            self::SHOW_STUB,
            'show.blade.php'
        );
    }

    /**
     * Genera _form.blade.php
     */
    private function generateForm(
        ModuleData $module
    ): string {

        return $this->writeView(
            $module,
            self::FORM_STUB,
            '_form.blade.php'
        );
    }

    /**
     * Construye las variables utilizadas por los stubs.
     *
     * @return array<string, mixed>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        $model = lcfirst(
            $module->modelClass()
        );

        $collection = $module->plural();

        return [

            'title'
            => $module->plural(),

            'description'
            => $module->description(),

            'module'
            => $module->routeName(),

            'singular'
            => $module->singular(),

            'plural'
            => $module->plural(),

            'model'
            => $model,

            'collection'
            => $this->camelPlural($collection),

            'columns'
            => count($module->columns()),

            'modelClass'
            => $module->modelClass(),

            'table'
            => $module->table(),

            'routeName'
            => $module->routeName(),

            'viewPrefix'
            => $module->viewPrefix(),


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



    /**
     * Escribe una vista Blade.
     */
    private function writeView(
        ModuleData $module,
        string $stub,
        string $filename
    ): string {

        $path = $module->viewPath()
            . DIRECTORY_SEPARATOR
            . $filename;

        $this->generateFile(
            $stub,
            $path,
            $this->buildVariables($module)
        );

        return $path;
    }
}
