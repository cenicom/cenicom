<?php

declare(strict_types=1);

namespace App\Providers;



use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registrar las dependencias de la aplicación.
     */
    public function register(): void
    {

    }

    /**
     * Inicializar servicios.
     */
    public function boot(): void {}
}
