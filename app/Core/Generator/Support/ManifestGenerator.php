<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use Illuminate\Support\Str;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente el manifiesto base de un módulo.
 *
 * Su responsabilidad consiste únicamente en construir la
 * estructura inicial del manifiesto y persistirla en el
 * directorio de módulos.
 *
 * @package App\Core\Generator\Support
 * @since 1.0.0
 */
final readonly class ManifestGenerator
{
    public function __construct(
        private FileWriter $writer,
    ) {
    }

    /**
     * Genera el manifiesto base del módulo.
     */
    public function generate(string $module): string
    {
        $manifest = $this->buildManifest($module);

        $json = json_encode(
            $manifest,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        $file = $this->manifestFile($module);

        $this->writer->write(
            $file,
            $json
        );

        return $file;
    }

    /**
     * Construye el manifiesto base.
     *
     * @return array<string,mixed>
     */
    private function buildManifest(string $module): array
    {
        $singular = Str::snake($module);
        $plural = Str::plural($singular);

        return [

            'identity' => [
                'name' => $module,
                'singular' => $singular,
                'plural' => $plural,
                'table' => $plural,
                'description' => "Module {$module}",
            ],

            'database' => [

            ],

            'fields' => [

            ],

            'relations' => [

            ],

            'validation' => [

            ],

            'permissions' => [

            ],

            'navigation' => [

            ],

            'generation' => [
                'routePrefix' => $plural,
                'routeName' => $plural,
                'viewPrefix' => $plural,

                'timestamps' => true,
                'softDeletes' => false,
                'uuid' => true,
                'api' => false,
                'tests' => true,
                'permissions' => true,
                'menu' => true,
                'icon' => 'bi-grid',
            ],

            'metadata' => [
                'version' => '1.0.0',
                'source' => 'generator',
            ],
        ];
    }

    /**
     * Obtiene la ruta del manifiesto.
     */
    private function manifestFile(string $module): string
    {
        return base_path(
            "modules/{$module}.json"
        );
    }
}
