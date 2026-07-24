<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Administra el archivo config/cn-bindings.php.
 *
 * Responsabilidades:
 *
 * - Leer bindings.
 * - Agregar bindings.
 * - Eliminar bindings.
 * - Evitar duplicados.
 * - Ordenar alfabéticamente.
 * - Persistir el archivo.
 *
 * No conoce:
 *
 * - ModuleData
 * - GeneratorResult
 * - Generators
 * * Stubs
 *
 * @package App\Core\Generator\Support
 * @since 2.0.0
 */
final readonly class BindingWriter
{
    /**
     * Constructor.
     */
    public function __construct(
        private FileWriter $fileWriter,
    ) {
    }

    /**
     * Agrega un binding.
     */
    public function add(
        string $interface,
        string $implementation,
    ): bool {
        $bindings = $this->load();

        if (isset($bindings[$interface])) {
            return false;
        }

        $bindings[$interface] = $implementation;

        $this->replace($bindings);

        return true;
    }

    /**
     * Elimina un binding.
     */
    public function remove(
        string $interface,
    ): bool {
        $bindings = $this->load();

        if (! isset($bindings[$interface])) {
            return false;
        }

        unset($bindings[$interface]);

        $this->replace($bindings);

        return true;
    }

    /**
     * Verifica si existe un binding.
     */
    public function exists(
        string $interface,
    ): bool {
        return isset($this->load()[$interface]);
    }

    /**
     * Devuelve todos los bindings.
     *
     * @return array<string,string>
     */
    public function all(): array
    {
        return $this->load();
    }

    /**
     * Reemplaza todos los bindings.
     *
     * @param array<string,string> $bindings
     */
    public function replace(
        array $bindings,
    ): void {
        $bindings = $this->normalize($bindings);

        $this->save($bindings);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos privados
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,string>
     */
    private function load(): array
    {
        $path = $this->configPath();

        if (! file_exists($path)) {
            return [];
        }

        $bindings = require $path;

        return is_array($bindings)
            ? $bindings
            : [];
    }

    /**
     * @param array<string,string> $bindings
     */
    private function save(
        array $bindings,
    ): void {
        $this->fileWriter->write(
            $this->configPath(),
            $this->render($bindings),
            overwrite: true,
        );
    }

    /**
     * @param array<string,string> $bindings
     */
    private function render(
        array $bindings,
    ): string {
        $content = <<<PHP
<?php

declare(strict_types=1);

return [

PHP;

        foreach ($bindings as $interface => $implementation) {

            $content .= PHP_EOL .
                "    {$interface}::class => {$implementation}::class,";
        }

        $content .= PHP_EOL . PHP_EOL . "];" . PHP_EOL;

        return $content;
    }

    /**
     * @param array<string,string> $bindings
     *
     * @return array<string,string>
     */
    private function normalize(
        array $bindings,
    ): array {
        ksort($bindings);

        return $bindings;
    }

    /**
     * Ruta absoluta del archivo.
     */
    private function configPath(): string
    {
        return config_path('cn-bindings.php');
    }
}
