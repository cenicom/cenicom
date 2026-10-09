<?php

declare(strict_types=1);

namespace App\Modules\Campus\Providers;

use App\Modules\Campus\Domain\Contracts\CampusCodeGeneratorInterface;
use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusIdGeneratorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Services\CampusCreator;
use App\Modules\Campus\Infrastructure\Identity\CampusCodeGenerator;
use App\Modules\Campus\Infrastructure\Identity\CampusCodeSequence;
use App\Modules\Campus\Infrastructure\Identity\CampusIdGenerator;
use App\Modules\Campus\Repositories\CampusRepository;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;
use App\Modules\Campus\Domain\Services\CampusService;
use Illuminate\Support\ServiceProvider;

final class CampusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CampusRepositoryInterface::class,
            CampusRepository::class,
        );

        $this->app->bind(
            CampusIdGeneratorInterface::class,
            CampusIdGenerator::class,
        );

        $this->app->bind(
            CampusCodeSequenceInterface::class,
            CampusCodeSequence::class,
        );

        $this->app->bind(
            CampusCodeGeneratorInterface::class,
            CampusCodeGenerator::class,
        );

        $this->app->bind(
            CampusCreatorInterface::class,
            CampusCreator::class,
        );

        $this->app->bind(
            CampusServiceInterface::class,
            CampusService::class,
        );
    }
}
