<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Address\Infrastructure\Persistence;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class AddressPersistenceTest extends TestCase
{
    private const TEST_ADDRESS = 'Carrera 10 # 20-30';
    private const TEST_NEIGHBORHOOD = 'Centro';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'cenicom',
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');
    }

    public function test_creates_address_with_real_geographic_reference(): void
    {
        $service = app(AddressServiceInterface::class);

        $address = $service->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => self::TEST_ADDRESS,
            'neighborhood' => self::TEST_NEIGHBORHOOD,
        ]);

        $this->assertNotNull($address->getKey());
        $this->assertSame(26, strlen((string) $address->getKey()));

        $this->assertDatabaseHas(
            'addresses',
            [
                'id' => $address->getKey(),
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'address' => self::TEST_ADDRESS,
                'neighborhood' => self::TEST_NEIGHBORHOOD,
            ],
            'mysql',
        );
    }

    public function test_rejects_incoherent_geographic_combination_without_persisting(): void
    {
        $service = app(AddressServiceInterface::class);

        $before = DB::connection('mysql')
            ->table('addresses')
            ->count();

        $this->expectException(\InvalidArgumentException::class);

        try {
            $service->create([
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 7,
                'address' => self::TEST_ADDRESS,
                'neighborhood' => self::TEST_NEIGHBORHOOD,
            ]);
        } finally {
            $after = DB::connection('mysql')
                ->table('addresses')
                ->count();

            $this->assertSame($before, $after);
        }
    }
}
