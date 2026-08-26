<?php

declare(strict_types=1);

namespace App\Modules\Institution\Providers;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Repositories\InstitutionRepository;
use Illuminate\Support\ServiceProvider;

final class InstitutionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
        $this->app->bind(
            InstitutionRepositoryInterface::class,
            InstitutionRepository::class,
        );
    }

    public function boot(
        InstitutionNavigation $definition,
        NavigationRegistrarInterface $registrar,
    ): void {

        $definition->register($registrar);
    }
}
