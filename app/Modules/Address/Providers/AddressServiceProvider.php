<?php

declare(strict_types=1);

namespace App\Modules\Address\Providers;

use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Domain\Contracts\GeographicReferenceInterface;
use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicReference;
use App\Modules\Address\Repositories\AddressRepository;
use App\Modules\Address\Services\AddressService;
use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicQuery;
use Illuminate\Support\ServiceProvider;

final class AddressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AddressRepositoryInterface::class,
            AddressRepository::class,
        );

        $this->app->bind(
            AddressServiceInterface::class,
            AddressService::class,
        );

        $this->app->bind(
            GeographicReferenceInterface::class,
            ImportedTablesGeographicReference::class,

        );
        $this->app->bind(
            GeographicQueryInterface::class,
            ImportedTablesGeographicQuery::class,
        );
    }
}
