<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Controller;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * el Controller del módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir imports.
 * - Construir nombre de la clase.
 * - Construir constructor.
 * - Construir métodos CRUD.
 * - Preparar variables para el StubManager.
 *
 * ControllerGenerator únicamente orquesta el proceso.
 */
final class ControllerBuilder
{
    /**
     * Punto de entrada.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return $this->buildVariables($module);
    }


    /**
     * Construye todas las variables del stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(ModuleData $module): array
    {
        return [

            // Bloques generados
            'namespace'
            => $this->buildNamespace($module),

            'imports'
            => $this->buildImports($module),

            'class'
            => $this->buildClassName($module),

            'constructor'
            => $this->buildConstructor($module),

            'methods'
            => $this->buildMethods($module),


            // Variables directas del stub

            'qualifiedServiceInterface'
            => $module->qualifiedServiceInterface(),

            'qualifiedStoreRequest'
            => $module->qualifiedStoreRequest(),

            'qualifiedUpdateRequest'
            => $module->qualifiedUpdateRequest(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'model'
            => $module->modelClass(),

            'controller'
            => $module->controllerClass(),

            'serviceInterface'
            => $module->serviceInterface(),

            'viewPrefix'
            => $module->viewPrefix(),

            'pluralVariable'
            => $module->pluralVariable(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

            'routeName'
            => $module->routeName(),

            'singular'
            => $module->singular(),

            'variable'
            => $module->variable(),

            'displayName'
            => $module->displayName(),
        ];
    }

    /**
     * Construye el namespace.
     */
    private function buildNamespace(ModuleData $module): string
    {
        return $module->controllerNamespace();
    }

    /**
     * Construye los imports del Controller.
     */
    private function buildImports(ModuleData $module): string
    {
        $imports = [

            'use App\Http\Controllers\Controller;',

            sprintf(
                'use %s;',
                $module->qualifiedService()
            ),

            sprintf(
                'use %s;',
                $module->qualifiedStoreRequest()
            ),

            sprintf(
                'use %s;',
                $module->qualifiedUpdateRequest()
            ),

        ];

        return implode(
            "\n",
            array_unique($imports)
        );
    }


    /**
     * Construye el nombre de la clase.
     */
    private function buildClassName(ModuleData $module): string
    {
        return $module->controllerClass();
    }


    /**
     * Construye el constructor del Controller.
     */
    private function buildConstructor(ModuleData $module): string
    {
    return <<<PHP
    public function __construct(
        private readonly {$module->serviceInterface()} \${$module->variable()}Service,
    ) {
    }
    PHP;
    }


    private function buildMethods(ModuleData $module): string
    {
        return $this->indent(
            implode("\n\n", array_filter([

                $this->buildIndexMethod($module),

                $this->buildCreateMethod($module),

                $this->buildStoreMethod($module),

                $this->buildShowMethod($module),

                $this->buildEditMethod($module),

                $this->buildUpdateMethod($module),

                $this->buildDestroyMethod($module),

            ]))
        );
    }

    /**
     * Construye el método index().
     */
    private function buildIndexMethod(ModuleData $module): string
    {
    return <<<PHP
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \${$module->pluralVariable()} = \$this->service->all();

        return view(
            '{$module->viewPrefix()}.index',
            compact('{$module->pluralVariable()}')
        );
    }
    PHP;
    }

    /**
     * Construye el método create().
     */
    private function buildCreateMethod(ModuleData $module): string
    {
    return <<<PHP
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(
            '{$module->viewPrefix()}.create'
        );
    }
    PHP;
    }

    /**
     * Construye el método store().
     */
    private function buildStoreMethod(ModuleData $module): string
    {
        $request = $module->storeRequestClass();

    return <<<PHP
    /**
     * Store a newly created resource.
     */
    public function store(
        {$request} \$request
    ) {
        \$this->service->create(
            \$request->validated()
        );

        return redirect()
            ->route('{$module->routeName()}.index');
    }
    PHP;
    }

    /**
     * Construye el método show().
     */
    private function buildShowMethod(ModuleData $module): string
    {
        $model = $module->modelClass();

        $variable = $module->variable();

    return <<<PHP
    /**
     * Muestra un recurso específico.
     */
    public function show(
        {$model} \${$variable}
    ): View {

        return view('{$module->viewPrefix()}.show', [
            '{$variable}' => \${$variable},
        ]);
    }
    PHP;
    }

    /**
     * Construye el método edit().
     */
    private function buildEditMethod(ModuleData $module): string
    {
        $model = $module->modelClass();

        $variable = $module->variable();

    return <<<PHP
    /**
     * Edita un recurso específico.
     */
    public function edit(
        {$model} \${$variable}
    ): View {

        return view('{$module->viewPrefix()}.edit', [
            '{$variable}' => \${$variable},
        ]);
    }
    PHP;
    }

    /**
     * Construye el método update().
     */
    private function buildUpdateMethod(ModuleData $module): string
    {
        $model = $module->modelClass();

        $variable = $module->variable();

    return <<<PHP
    /**
     * Actualiza un recurso específico.
     */
    public function update(
        {$module->updateRequestClass()} \$request,
        {$model} \${$variable}
    ): RedirectResponse {

        \$this->service->update(
            \${$variable},
            \$request->validated()
        );

        return redirect()
            ->route('{$module->routeName()}.index')
            ->with(
                'success',
                '{$module->singular()} actualizado correctamente.'
            );
    }
    PHP;
    }

    /**
     * Construye el método destroy().
     */
    private function buildDestroyMethod(ModuleData $module): string
    {
        $model = $module->modelClass();

        $variable = $module->variable();

    return <<<PHP
    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        {$model} \${$variable}
    ): RedirectResponse {

        \$this->service->destroy(
            \${$variable}
        );

        return redirect()
            ->route('{$module->routeName()}.index')
            ->with(
                'success',
                '{$module->singular()} eliminado correctamente.'
            );
    }
    PHP;
    }


    /**
     * Aplica indentación al código generado.
     */
    private function indent(string $text, int $level = 1): string
    {
        $indent = str_repeat('    ', $level);

        return implode(
            "\n",
            array_map(
                static fn(string $line): string => $indent . $line,
                explode("\n", $text)
            )
        );
    }
}
