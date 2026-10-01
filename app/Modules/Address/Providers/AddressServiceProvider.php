<?php

declare(strict_types=1);

namespace App\Modules\Address\Providers;

use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Repositories\AddressRepository;
use App\Modules\Address\Services\AddressService;
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
    }
}
