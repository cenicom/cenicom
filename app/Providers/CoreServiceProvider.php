<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Contracts\NavigationAuthorizationInterface;
use App\Core\Navigation\Authorization\NavigationAuthorization;
use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            NavigationAuthorizationInterface::class,
            NavigationAuthorization::class,
        );
    }

    public function boot(): void
    {
    }
}
