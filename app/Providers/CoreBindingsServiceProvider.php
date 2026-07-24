<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class CoreBindingsServiceProvider extends ServiceProvider
{
    /**
     * Registra todos los bindings del Core.
     */
    public function register(): void
    {
        $bindings = config('cn-bindings', []);

        if (! is_array($bindings)) {
            return;
        }

        foreach ($bindings as $abstract => $concrete) {

            $this->app->bind(
                $abstract,
                $concrete,
            );
        }
    }
}
