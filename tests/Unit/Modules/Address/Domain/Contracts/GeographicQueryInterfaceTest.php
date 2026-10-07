<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Address\Domain\Contracts;

use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Address\Domain\DTOs\GeographicOption;
use PHPUnit\Framework\TestCase;

final class GeographicQueryInterfaceTest extends TestCase
{
    public function test_countries_returns_geographic_options(): void
    {
        $query = new class implements GeographicQueryInterface {
            public function countries(): array
            {
                return [
                    new GeographicOption(1, 'Colombia'),
                    new GeographicOption(2, 'Ecuador'),
                ];
            }

            public function statesByCountry(int $countryId): array
            {
                return [];
            }

            public function citiesByState(int $stateId): array
            {
                return [];
            }
        };

        $result = $query->countries();

        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(
            GeographicOption::class,
            $result
        );
        self::assertSame(1, $result[0]->id);
        self::assertSame('Colombia', $result[0]->name);
    }

    public function test_states_by_country_returns_geographic_options(): void
    {
        $query = new class implements GeographicQueryInterface {
            public function countries(): array
            {
                return [];
            }

            public function statesByCountry(int $countryId): array
            {
                return [
                    new GeographicOption(1, 'Huila'),
                    new GeographicOption(2, 'Caquetá'),
                ];
            }

            public function citiesByState(int $stateId): array
            {
                return [];
            }
        };

        $result = $query->statesByCountry(49);

        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(
            GeographicOption::class,
            $result
        );
        self::assertSame(1, $result[0]->id);
        self::assertSame('Huila', $result[0]->name);
    }

    public function test_cities_by_state_returns_geographic_options(): void
    {
        $query = new class implements GeographicQueryInterface {
            public function countries(): array
            {
                return [];
            }

            public function statesByCountry(int $countryId): array
            {
                return [];
            }

            public function citiesByState(int $stateId): array
            {
                return [
                    new GeographicOption(101, 'Neiva'),
                    new GeographicOption(102, 'Campoalegre'),
                ];
            }
        };

        $result = $query->citiesByState(1);

        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(
            GeographicOption::class,
            $result
        );
        self::assertSame(101, $result[0]->id);
        self::assertSame('Neiva', $result[0]->name);
    }
}
