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

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
    ) {
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
    private function generateStoreRequest(
        ModuleData $module
    ): GeneratorResult {
        $file = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->storeRequestClass()
            . '.php';

        return $this->generateResult(
            self::STORE_STUB,
            $file,
            $this->buildVariables(
                $module,
                $module->storeRequestClass()
            )
        );
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
        ModuleData $module
    ): GeneratorResult {
        $file = $module->requestPath()
            . DIRECTORY_SEPARATOR
            . $module->updateRequestClass()
            . '.php';

        return $this->generateResult(
            self::UPDATE_STUB,
            $file,
            $this->buildVariables(
                $module,
                $module->updateRequestClass()
            )
        );
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

            'singular'
            => $module->singular(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

            'rules' => $this->buildRules($module),

        ];
    }

    private function buildRule(
        ColumnDefinition $column,
        ModuleData $module
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

        if ($unique = $this->resolveUniqueRule($column, $module)) {
            $rules[] = $unique;
        }

        return "['" . implode("', '", $rules) . "']";
    }

    private function buildRules(
        ModuleData $module
    ): string {

        $rules = [];

        foreach ($module->columns() as $column) {

            if (! $this->shouldGenerateRule($column)) {
                continue;
            }

            $rules[] = sprintf(
                "            '%s' => %s,",
                $column->name(),
                $this->buildRule(
                    $column,
                    $module
                )
            );
        }

        return implode(
            PHP_EOL,
            $rules
        );
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

            FieldType::BIG_INTEGER => ['bigInteger'],

            FieldType::FLOAT => ['float'],

            FieldType::DOUBLE => ['double'],

            FieldType::JSON => ['json'],

            FieldType::ENUM => ['enum'],

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

    /**
     * Resuelve la regla unique de Laravel.
     */
    private function resolveUniqueRule(
        ColumnDefinition $column,
        ModuleData $module
    ): ?string {

        if (! $column->unique()) {
            return null;
        }

        return sprintf(
            'unique:%s,%s',
            $module->table(),
            $column->name()
        );
    }
}
