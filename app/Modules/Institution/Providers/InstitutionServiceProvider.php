<?php

declare(strict_types=1);

namespace App\Modules\Institution\Providers;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Domain\Services\InstitutionService;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use App\Modules\Institution\Repositories\InstitutionRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Infrastructure\Identity\InstitutionCodeGenerator;
use App\Modules\Institution\Infrastructure\Identity\InstitutionCodeSequence;
use App\Modules\Institution\Infrastructure\Identity\InstitutionIdGenerator;
use App\Modules\Institution\Domain\Services\InstitutionCreator;
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

        $this->app->bind(
            InstitutionServiceInterface::class,
            InstitutionService::class,
        );

        $this->app->bind(
            InstitutionIdGeneratorInterface::class,
            InstitutionIdGenerator::class,
        );

        $this->app->bind(
            InstitutionCodeSequenceInterface::class,
            InstitutionCodeSequence::class,
        );

        $this->app->bind(
            InstitutionCodeGeneratorInterface::class,
            InstitutionCodeGenerator::class,
        );

        $this->app->bind(
            InstitutionCreatorInterface::class,
            InstitutionCreator::class,
        );
    }

    public function boot(
        InstitutionNavigation $definition,
        NavigationRegistrarInterface $registrar,
    ): void {

        $definition->register($registrar);
    }
}
