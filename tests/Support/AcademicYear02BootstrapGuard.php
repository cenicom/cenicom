<?php

declare(strict_types=1);

namespace Tests\Support;

final class AcademicYear02BootstrapGuard
{
    /**
     * @param array<string, mixed> $processEnvironment
     * @param list<string> $environmentFiles
     * @return list<string>
     */
    public static function findConfigurationCacheOverrides(
        array $processEnvironment,
        array $environmentFiles,
    ): array {
        $errors = [];

        $values = [
            $processEnvironment['APP_CONFIG_CACHE'] ?? null,
            $_ENV['APP_CONFIG_CACHE'] ?? null,
            $_SERVER['APP_CONFIG_CACHE'] ?? null,
            getenv('APP_CONFIG_CACHE') === false
                ? null
                : getenv('APP_CONFIG_CACHE'),
        ];

        foreach ($values as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                $errors[] =
                    'APP_CONFIG_CACHE: no se permiten rutas personalizadas.';
                break;
            }
        }

        foreach ($environmentFiles as $file) {
            if (! is_file($file) || ! is_readable($file)) {
                continue;
            }

            $lines = file($file, FILE_IGNORE_NEW_LINES);

            if ($lines === false) {
                $errors[] =
                    'No fue posible inspeccionar el archivo de entorno: '
                    . basename($file);
                continue;
            }

            foreach ($lines as $line) {
                if (preg_match(
                    '/^\s*(?:export\s+)?APP_CONFIG_CACHE\s*=/',
                    $line,
                ) !== 1) {
                    continue;
                }

                $errors[] =
                    'APP_CONFIG_CACHE definido en '
                    . basename($file)
                    . ': no se permiten definiciones personalizadas.';

                break;
            }
        }

        return array_values(array_unique($errors));
    }
}
