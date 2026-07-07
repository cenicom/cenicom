<?php

namespace App\Providers;

use App\Support\Navigation\NavigationManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(NavigationManager::class, function () {
            return new NavigationManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        View::composer('*', function ($view) {

            $nav = app(NavigationManager::class);

            $view->with('navigation', $nav->grouped());
        });
    }
}
