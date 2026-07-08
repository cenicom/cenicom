<?php

namespace App\Modules\Usuarios\Providers;

use Illuminate\Support\ServiceProvider;

class UsuariosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(
            __DIR__.'/../Routes/web.php'
        );

        $this->loadViewsFrom(
            __DIR__.'/../Resources/views',
            'usuarios'
        );
    }
}
