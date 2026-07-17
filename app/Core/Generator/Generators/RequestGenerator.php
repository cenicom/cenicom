<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Generador automático de Form Requests Laravel.
 *
 * Genera:
 *
 * Store{Module}Request.php
 * Update{Module}Request.php
 *
 * Ubicación destino:
 *
 * app/Http/Requests/{Module}
 *
 * ==========================================================
 */
final class RequestGenerator extends BaseGenerator
{
    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
        );
    }


    /**
     * Determina si este generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Ejecuta la generación completa del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        $files = [];

        $files[] = $this->generateStoreRequest($module);

        $files[] = $this->generateUpdateRequest($module);

        $result = new GeneratorResult();

        foreach ($files as $file) {
            $result->addCreated($file);
        }

        return $result;
    }


    /**
     * Genera StoreRequest.
     */
    private function generateStoreRequest(
        ModuleData $module
    ): string {

        $path = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->storeRequestClass()
            . '.php';

        $this->generateFile(
            'requests/store',
            $path,
            $this->buildVariables(
                $module,
                $module->storeRequestClass()
            )
        );

        return $path;
    }


    /**
     * Genera UpdateRequest.
     */
    private function generateUpdateRequest(
        ModuleData $module
    ): string {

        $path = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->updateRequestClass()
            . '.php';

        $this->generateFile(
            'requests/update',
            $path,
            $this->buildVariables(
                $module,
                $module->updateRequestClass()
            )
        );

        return $path;
    }

    /**
     * Construye las variables utilizadas por los stubs.
     *
     * @return array<string,string>
     */

    private function buildVariables(
        ModuleData $module,
        string $class
    ): array {

        return [

            'namespace' => $module->requestNamespace(),

            'className' => $class,

            'rules' => $this->resolveRules($module),

            'singular'
            => $module->singular(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

        ];
    }


    /**
     * Construye reglas Laravel.
     */
    private function resolveRules(
        ModuleData $module
    ): string {

        $rules = [];

        foreach ($module->columns() as $column) {

            $rules[] = sprintf(
                "            '%s' => ['required'],",
                $column->name(),
                $this->buildRule($column)
            );
        }

        return implode(
            PHP_EOL,
            $rules
        );
    }

    private function buildRule(
        ColumnDefinition $column
    ): string {

        $rules = [];

        if (! $column->nullable()) {
            $rules[] = 'required';
        }

        switch ($column->type()) {

            case 'string':
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;

            case 'text':
                $rules[] = 'string';
                break;

            case 'integer':
                $rules[] = 'integer';
                break;

            case 'decimal':
                $rules[] = 'numeric';
                break;

            case 'boolean':
                $rules[] = 'boolean';
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'datetime':
                $rules[] = 'date';
                break;

            case 'uuid':
                $rules[] = 'uuid';
                break;

            case 'email':
                $rules[] = 'email';
                break;

            default:
                $rules[] = 'string';
        }

        return "['" . implode("', '", $rules) . "']";
    }



}
