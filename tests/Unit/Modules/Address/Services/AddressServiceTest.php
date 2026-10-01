<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Address\Services;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\ServiceInterface;
use App\Core\Services\BaseService;
use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Services\AddressService;
use Mockery;
use Tests\TestCase;

final class AddressServiceTest extends TestCase
{
    public function test_service_implements_address_service_contract(): void
    {
        $repository = Mockery::mock(
            AddressRepositoryInterface::class,
        );

        $service = new AddressService(
            $repository,
        );

        self::assertInstanceOf(
            AddressServiceInterface::class,
            $service,
        );
    }

    public function test_service_extends_base_service(): void
    {
        $repository = Mockery::mock(
            AddressRepositoryInterface::class,
        );

        $service = new AddressService(
            $repository,
        );

        self::assertInstanceOf(
            BaseService::class,
            $service,
        );

        self::assertInstanceOf(
            ServiceInterface::class,
            $service,
        );

        self::assertInstanceOf(
            RepositoryInterface::class,
            $repository,
        );
    }
}
