<?php

declare(strict_types=1);

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$reflection = new ReflectionClass($kernel);
$property = $reflection->getProperty('bootstrappers');
$property->setAccessible(true);

foreach ($property->getValue($kernel) as $bootstrapper) {
    $name = basename(str_replace('\\', '/', $bootstrapper));

    echo "BEFORE={$name}";

    if ($app->bound('config')) {
        $bindings = $app->make('config')->get('cn-bindings', []);

        echo ' CONFIG=' . count(is_array($bindings) ? $bindings : []);
        echo ' CITY=' . (
            isset(
                $bindings[
                    App\Modules\City\Domain\Contracts\CityServiceInterface::class
                ]
            )
                ? 'YES'
                : 'NO'
        );
    } else {
        echo ' CONFIG=NOT_REGISTERED';
    }

    echo PHP_EOL;

    $app->make($bootstrapper)->bootstrap($app);

    if ($app->bound('config')) {
        $bindings = $app->make('config')->get('cn-bindings', []);

        echo "AFTER={$name}";
        echo ' CONFIG=' . count(is_array($bindings) ? $bindings : []);
        echo ' CITY=' . (
            isset(
                $bindings[
                    App\Modules\City\Domain\Contracts\CityServiceInterface::class
                ]
            )
                ? 'YES'
                : 'NO'
        );
        echo PHP_EOL;
    } else {
        echo "AFTER={$name} CONFIG=NOT_REGISTERED" . PHP_EOL;
    }
}
