<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Observer;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * el Observer del módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir imports.
 * - Construir nombre de la clase.
 * - Construir métodos del observer.
 * - Preparar variables para el StubManager.
 *
 * ObserverGenerator únicamente orquesta el proceso.
 */
final class ObserverBuilder
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
            'imports'   => $this->buildImports($module),
            'class'     => $this->buildClassName($module),
            'methods'   => $this->buildMethods($module),
        ];
    }

    /**
     * Construye el namespace.
     */
    private function buildNamespace(ModuleData $module): string
    {
        return $module->observerNamespace();
    }

    /**
     * Construye los imports del Observer.
     */
    private function buildImports(ModuleData $module): string
    {
        $imports = [

            sprintf(
                'use %s;',
                $module->qualifiedModel()
            ),

            'use Illuminate\\Database\\Eloquent\\Model;',
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
        return $module->observerClass();
    }

    /**
     * Construye todos los métodos.
     */
    private function buildMethods(ModuleData $module): string
    {
        return $this->indent(
            implode("\n\n", array_filter([

                $this->buildCreatingMethod($module),

                $this->buildCreatedMethod($module),

                $this->buildUpdatingMethod($module),

                $this->buildUpdatedMethod($module),

                $this->buildDeletingMethod($module),

                $this->buildDeletedMethod($module),

                $this->buildRestoringMethod($module),

                $this->buildRestoredMethod($module),

                $this->buildForceDeletedMethod($module),

            ]))
        );
    }

    /**
     * Evento creating().
     */
    private function buildCreatingMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "creating" event.
     */
    public function creating({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento creating().
    }
    PHP;
    }

    /**
     * Evento created().
     */
    private function buildCreatedMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "created" event.
     */
    public function created({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento created().
    }
    PHP;
    }

    /**
     * Construye el método updating().
     */
    private function buildUpdatingMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "updating" event.
     */
    public function updating({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento updating().
    }
    PHP;
    }

    /**
     * Evento updated().
     */
    private function buildUpdatedMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "updated" event.
     */
    public function updated({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento updated().
    }
    PHP;
    }

    /**
     * Construye el método deleting().
     */
    private function buildDeletingMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "deleting" event.
     */
    public function deleting({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento deleting().
    }
    PHP;
    }

    /**
     * Evento deleted().
     */
    private function buildDeletedMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "deleted" event.
     */
    public function deleted({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento deleted().
    }
    PHP;
    }

    /**
     * Construye el método restoring().
     */
    private function buildRestoringMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "restoring" event.
     */
    public function restoring({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento restoring().
    }
    PHP;
    }

    /**
     * Evento restored().
     */
    private function buildRestoredMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "restored" event.
     */
    public function restored({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento restored().
    }
    PHP;
    }

    /**
     * Evento forceDeleted().
     */
    private function buildForceDeletedMethod(ModuleData $module): string
    {
        $model = $module->modelClass();
        $variable = $module->variable();

        return <<<PHP
    /**
     * Handle the {$model} "force deleted" event.
     */
    public function forceDeleted({$model} \${$variable}): void
    {
        // TODO: Implementar lógica del evento forceDeleted().
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
