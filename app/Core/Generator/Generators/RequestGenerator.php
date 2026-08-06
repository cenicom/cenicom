<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;

use App\Core\Generator\DTO\ModuleData;

use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Request\RequestBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

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
    private const STORE_STUB = 'requests/store';
    private const UPDATE_STUB = 'requests/update';

    public function __construct(StubManager $stubManager, FileWriter $fileWriter,
        PresentationFactory $presentationFactory, GeneratorValidator $validator,
        private readonly RequestBuilder $builder, )
    {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
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

        $result = new GeneratorResult();

        $result->merge(
            $this->generateStoreRequest($module)
        );

        $result->merge(
            $this->generateUpdateRequest($module)
        );

        return $result;
    }

    /**
     * Genera StoreRequest.
     */
    private function generateStoreRequest(ModuleData $module): GeneratorResult
    {
        $file = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->storeRequestClass()
            . '.php';

        return $this->generateResult(
            self::STORE_STUB,
            $file,
            $this->builder->build(
                $module,
                $module->storeRequestClass()
            )
        );
    }

    /**
     * Genera UpdateRequest.
     */
    private function generateUpdateRequest(ModuleData $module): GeneratorResult
    {
        $file = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->updateRequestClass()
            . '.php';

        return $this->generateResult(
            self::UPDATE_STUB,
            $file,
            $this->builder->build(
                $module,
                $module->updateRequestClass()
            )
        );
    }

}
