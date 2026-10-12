<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);

$expectedEnvironment = [
    'APP_ENV' => 'testing',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3308',
    'DB_DATABASE' => 'cenicom_aca_year02_isolated_test',
    'DB_USERNAME' => 'aca_year02_test',
    'DB_URL' => '',
];

$errors = [];

foreach ($expectedEnvironment as $name => $expectedValue) {
    $actualValue = getenv($name);

    if ($actualValue !== $expectedValue) {
        $errors[] = sprintf(
            '%s: configuración no autorizada; valor esperado %s.',
            $name,
            var_export($expectedValue, true),
        );
    }
}

$password = getenv('DB_PASSWORD');

if ($password === false || $password === '') {
    $errors[] =
        'DB_PASSWORD: debe estar definida localmente y no vacía.';
}

/*
 * No aceptar rutas alternativas para la configuración en caché.
 * Laravel podría cargar valores que contradigan las variables verificadas.
 */
$configCacheOverride = getenv('APP_CONFIG_CACHE');

if ($configCacheOverride !== false && $configCacheOverride !== '') {
    $errors[] =
        'APP_CONFIG_CACHE: no se permiten rutas personalizadas en ACA-YEAR-02.';
} elseif (is_file($projectRoot . '/bootstrap/cache/config.php')) {
    $errors[] =
        'Configuración en caché detectada; ACA-YEAR-02 no puede continuar.';
}

if ($errors !== []) {
    fwrite(
        STDERR,
        "ACA-YEAR-02: configuración de aislamiento rechazada.\n"
        . implode("\n", $errors)
        . "\n",
    );

    exit(1);
}

require_once $projectRoot . '/vendor/autoload.php';