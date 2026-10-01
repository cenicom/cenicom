<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Address\Repositories;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Repositories\BaseRepository;
use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Models\Address;
use App\Modules\Address\Repositories\AddressRepository;
use Tests\TestCase;

final class AddressRepositoryTest extends TestCase
{
    public function test_repository_implements_address_repository_contract(): void
    {
        $repository = new AddressRepository(
            new Address(),
        );

        self::assertInstanceOf(
            AddressRepositoryInterface::class,
            $repository,
        );
    }

    public function test_repository_extends_base_repository(): void
    {
        $repository = new AddressRepository(
            new Address(),
        );

        self::assertInstanceOf(
            BaseRepository::class,
            $repository,
        );

        self::assertInstanceOf(
            RepositoryInterface::class,
            $repository,
        );
    }
}
