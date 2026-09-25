<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\View\Components\Cn\Crud\Modal;
use App\View\Components\Layouts\App;
use App\View\Components\Cn\FormActions;
use Illuminate\Support\Facades\Blade;

class CNFrameworkServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios del Framework.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar el Framework.
     */
    public function boot(): void
    {
        $this->bootFramework();

        Blade::component(
            'components.cn.crud.modal',
            Modal::class
        );

        Blade::component(
            'layout.app',
            App::class
        );

        Blade::component(
            'cn-form-actions',
            FormActions::class
        );
    }

    /**
     * Registrar componentes internos.
     */
    protected function registerFramework(): void
    {
        //
    }

    /**
     * Inicializar componentes internos.
     */
    protected function bootFramework(): void
    {
        //
    }
}
