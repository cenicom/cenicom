<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
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
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory
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

        $this->generateStoreRequest($module, $result);

        $this->generateUpdateRequest($module, $result);

        return $result;
    }

    /**
     * Genera StoreRequest.
     */
    private function generateStoreRequest(
        ModuleData $module,
        GeneratorResult $result
    ): void {

        $file = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->storeRequestClass()
            . '.php';

        if (
            $this->generateFile(
                'requests/store',
                $file,
                $this->buildVariables(
                    $module,
                    $module->storeRequestClass()
                )
            )
        ) {
            $result->addCreated($file);
        } else {
            $result->addSkipped($file);
        }
    }

    private function buildRules(ModuleData $module): string
    {
        $rules = [];

        foreach ($module->columns() as $column) {

            if (!$this->shouldGenerateRule($column)) {
                continue;
            }

            $rules[] = sprintf(
                "            '%s' => %s,",
                $column->name(),
                $this->buildRule($column)
            );
        }

        return implode(PHP_EOL, $rules);
    }

    private function shouldGenerateRule(
        ColumnDefinition $column
    ): bool {

        return $column->shouldAppearInForm();
    }

    /**
     * Genera UpdateRequest.
     */
    private function generateUpdateRequest(
        ModuleData $module,
        GeneratorResult $result
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

        $rules[] = $this->resolveRequiredRule($column);

        $rules = array_merge(
            $rules,
            $this->resolveTypeRules($column)
        );

        if ($length = $this->resolveLengthRule($column)) {
            $rules[] = $length;
        }

        if ($foreign = $this->resolveForeignRule($column)) {
            $rules[] = $foreign;
        }

        return "['" . implode("', '", $rules) . "']";
    }

    private function resolveRequiredRule(
        ColumnDefinition $column
    ): string {

        return $column->nullable()
            ? 'nullable'
            : 'required';
    }

    private function resolveLengthRule(
        ColumnDefinition $column
    ): ?string {

        return $column->length() !== null
            ? 'max:' . $column->length()
            : null;
    }

    private function resolveTypeRules(
        ColumnDefinition $column
    ): array {

        return match ($column->type()) {

            FieldType::STRING => ['string'],

            FieldType::TEXT => ['string'],

            FieldType::INTEGER => ['integer'],

            FieldType::DECIMAL => ['numeric'],

            FieldType::BOOLEAN => ['boolean'],

            FieldType::DATE => ['date'],

            FieldType::DATETIME => ['date'],

            FieldType::UUID => ['uuid'],

            FieldType::EMAIL => ['email'],

            default => ['string'],
        };
    }

    private function resolveForeignRule(
        ColumnDefinition $column
    ): ?string {

        if (!$column->isForeignKey()) {
            return null;
        }

        return sprintf(
            'exists:%s,%s',
            $column->on(),
            $column->references()
        );
    }







}
