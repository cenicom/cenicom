<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Policies;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * la Policy del módulo.
 *
 * PolicyGenerator únicamente orquesta el proceso.
 */
final class PolicyBuilder
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

            'namespace' => $this->buildNamespace($module),

            'imports' => $this->buildImports($module),

            'class' => $this->buildClassName($module),

            'methods' => $this->buildPolicyMethods($module),

        ];
    }

    /**
     * Construye el namespace.
     */
    private function buildNamespace(ModuleData $module): string
    {
        return $module->policyNamespace();
    }

    /**
     * Construye los imports.
     */
    private function buildImports(ModuleData $module): string
    {
        return implode("\n", [
            'use App\Models\User;',
            sprintf(
                'use %s\\%s;',
                $module->modelNamespace(),
                $module->modelClass(),
            ),
        ]);
    }

    /**
     * Construye el nombre de la clase.
     */
    private function buildClassName(ModuleData $module): string
    {
        return $module->policyClass();
    }

    /**
     * Construye los métodos de la Policy.
     */
    private function buildPolicyMethods(ModuleData $module): string
    {
        return $this->indent(
            implode("\n\n", array_filter([

                $this->buildViewAnyMethod($module),

                $this->buildViewMethod($module),

                $this->buildCreateMethod($module),

                $this->buildUpdateMethod($module),

                $this->buildDeleteMethod($module),

                $this->buildRestoreMethod($module),

                $this->buildForceDeleteMethod($module),

            ]))
        );
    }

    /**
     * Construye el método viewAny().
     */
    private function buildViewAnyMethod(ModuleData $module): string
    {
    return <<<PHP
    /**
     * Determina si el usuario puede ver cualquier registro.
     */
    public function viewAny(User \$user): bool
    {
        return true;
    }
    PHP;
    }

    /**
     * Construye el método view().
     */
    private function buildViewMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

    return <<<PHP
    /**
     * Determina si el usuario puede ver el registro.
     */
    public function view(
        User \$user,
        {$model} \${$variable}
    ): bool {
        return true;
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
     * Determina si el usuario puede crear registros.
     */
    public function create(User \$user): bool
    {
        return true;
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
     * Determina si el usuario puede actualizar el registro.
     */
    public function update(
        User \$user,
        {$model} \${$variable}
    ): bool {
        return true;
    }
    PHP;
    }

    /**
     * Construye el método delete().
     */
    private function buildDeleteMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

    return <<<PHP
    /**
     * Determina si el usuario puede eliminar el registro.
     */
    public function delete(
        User \$user,
        {$model} \${$variable}
    ): bool {
        return true;
    }
    PHP;
    }

    /**
     * Construye el método restore().
     */
    private function buildRestoreMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

    return <<<PHP
    /**
     * Determina si el usuario puede restaurar el registro.
     */
    public function restore(
        User \$user,
        {$model} \${$variable}
    ): bool {
        return true;
    }
    PHP;
    }

    /**
     * Construye el método forceDelete().
     */
    private function buildForceDeleteMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

    return <<<PHP
    /**
     * Determina si el usuario puede eliminar definitivamente el registro.
     */
    public function forceDelete(
        User \$user,
        {$model} \${$variable}
    ): bool {
        return true;
    }
    PHP;
    }

    /**
     * Aplica indentación al texto generado.
     */
    private function indent(string $text, int $level = 1): string
    {
        $indent = str_repeat('    ', $level);

        return implode(
            "\n",
            array_map(
                static fn(string $line): string => $indent . $line,
                explode("\n", $text),
            )
        );
    }
}
