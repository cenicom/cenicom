<?php

declare(strict_types=1);

namespace App\Providers;


use App\Core\Contracts\TestFormRepositoryInterface;
use App\Core\Repositories\TestFormRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registrar las dependencias de la aplicación.
     */
    public function register(): void
    {
        $this->app->bind(
            TestFormRepositoryInterface::class,
            TestFormRepository::class
        );
    }

    /**
     * Inicializar servicios.
     */
    public function boot(): void {}
}
