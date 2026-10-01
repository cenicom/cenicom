<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Address\Models;

use App\Modules\Address\Models\Address;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Tests\TestCase;

final class AddressTest extends TestCase
{
    public function test_address_uses_ulids(): void
    {
        $address = new Address();

        self::assertContains(
            HasUlids::class,
            class_uses_recursive($address),
        );
    }

    public function test_address_uses_addresses_table(): void
    {
        $address = new Address();

        self::assertSame(
            'addresses',
            $address->getTable(),
        );
    }

    public function test_address_uses_string_non_incrementing_primary_key(): void
    {
        $address = new Address();

        self::assertSame(
            'string',
            $address->getKeyType(),
        );

        self::assertFalse(
            $address->getIncrementing(),
        );
    }

    public function test_address_exposes_expected_fillable_attributes(): void
    {
        $address = new Address();

        self::assertSame(
            [
                'id',
                'country_id',
                'state_id',
                'city_id',
                'address',
                'neighborhood',
            ],
            $address->getFillable(),
        );
    }
}
