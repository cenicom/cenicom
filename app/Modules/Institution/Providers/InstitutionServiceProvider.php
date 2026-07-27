<?php

declare(strict_types=1);

namespace App\Modules\Institution\Providers;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use Illuminate\Support\ServiceProvider;

final class InstitutionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
        logger()->info(__METHOD__);
    }

    public function boot(
        InstitutionNavigation $definition,
        NavigationRegistrarInterface $registrar,
    ): void {

        $definition->register($registrar);
    }
}
