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
        protected StubManager $stubManager,
        protected FileWriter $fileWriter
    ) {
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

        $content = $this->stubManager->render(
            'requests/store',
            [
                'namespace' => $this->resolveNamespace($module),
                'className' => "Store{$module->name()}Request",
                'rules' => $this->resolveRules($module),
            ]
        );


        return $this->writeRequest(
            $module,
            "Store{$module->name()}Request.php",
            $content
        );
    }


    /**
     * Genera UpdateRequest.
     */
    private function generateUpdateRequest(
        ModuleData $module
    ): string {

        $content = $this->stubManager->render(
            'requests/update',
            [
                'namespace' => $this->resolveNamespace($module),
                'className' => "Update{$module->name()}Request.php",
                'rules' => $this->resolveRules($module),
            ]
        );


        return $this->writeRequest(
            $module,
            "Update{$module->name()}Request.php",
            $content
        );
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


    /**
     * Determina namespace destino.
     */
    private function resolveNamespace(
        ModuleData $module
    ): string {

        return $module->requestNamespace();
    }


    /**
     * Escribe archivo generado.
     */
    private function writeRequest(
        ModuleData $module,
        string $filename,
        string $content
    ): string {

        $path = base_path(
            "app/Http/Requests/{$module->name()}/{$filename}"
        );


        $this->fileWriter->write(
            $path,
            $content
        );


        return $path;
    }
}
