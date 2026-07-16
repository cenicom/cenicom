<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
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


        foreach ($module->fields() as $field) {

            $rules[] = sprintf(
                "            '%s' => ['required'],",
                $field['name']
            );
        }


        return implode(
            PHP_EOL,
            $rules
        );
    }



}
