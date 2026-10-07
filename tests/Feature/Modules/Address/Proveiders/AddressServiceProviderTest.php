<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Address\Proveiders;

use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Address\Domain\Contracts\GeographicReferenceInterface;
use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicQuery;
use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicReference;
use App\Modules\Address\Repositories\AddressRepository;
use App\Modules\Address\Services\AddressService;
use Tests\TestCase;

final class AddressServiceProviderTest extends TestCase
{
    public function test_address_repository_interface_resolves_to_repository(): void
    {
        $repository = app(AddressRepositoryInterface::class);

        $this->assertInstanceOf(
            AddressRepository::class,
            $repository,
        );
    }

    public function test_address_service_interface_resolves_to_service(): void
    {
        $service = app(AddressServiceInterface::class);

        $this->assertInstanceOf(
            AddressService::class,
            $service,
        );
    }

    public function test_geographic_reference_interface_resolves_to_reference(): void
    {
        $reference = app(GeographicReferenceInterface::class);

        $this->assertInstanceOf(
            ImportedTablesGeographicReference::class,
            $reference,
        );
    }

    public function test_geographic_query_interface_resolves_to_query(): void
    {
        $query = app(GeographicQueryInterface::class);

        $this->assertInstanceOf(
            ImportedTablesGeographicQuery::class,
            $query,
        );
    }
}
