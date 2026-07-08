<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/*
|--------------------------------------------------------------------------
| Contracts
|--------------------------------------------------------------------------
*/

use App\Contracts\CurrencyRepositoryInterface;
use App\Contracts\CurrencyServiceInterface;

/*
|--------------------------------------------------------------------------
| Repositories
|--------------------------------------------------------------------------
*/

use App\Repositories\CurrencyRepository;

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

use App\Services\CurrencyService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registrar las dependencias de la aplicación.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Currency Module
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            CurrencyRepositoryInterface::class,
            CurrencyRepository::class
        );

        $this->app->singleton(
            CurrencyServiceInterface::class,
            CurrencyService::class
        );
    }

    /**
     * Inicializar servicios.
     */
    public function boot(): void
    {
        //
    }
}
