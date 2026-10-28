<?php

declare(strict_types=1);

namespace App\Modules\Academic\Providers;

use App\Modules\Academic\Domain\Services\AcademicYearCreator;
use App\Modules\Academic\Repositories\AcademicYearRepository;
use Illuminate\Support\ServiceProvider;

final class AcademicServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AcademicYearRepository::class,
        );

        $this->app->bind(
            AcademicYearCreator::class,
        );
    }
}
