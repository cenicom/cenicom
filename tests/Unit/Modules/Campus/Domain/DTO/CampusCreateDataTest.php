<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\DTO;

use App\Modules\Campus\Domain\DTO\CampusCreateData;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CampusCreateDataTest extends TestCase
{
    public function test_accepts_valid_data(): void
    {
        $data = new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESBSC',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
            ministryCode: 'MIN-001',
        );

        $this->assertSame('01JTESTINSTITUTION', $data->institutionId);
        $this->assertSame('IESBSC', $data->institutionShortCode);
        $this->assertSame('CENTRAL', $data->shortCode);
        $this->assertSame('Campus Central', $data->name);
        $this->assertSame('01JTESTADDRESS', $data->addressId);
        $this->assertSame('MIN-001', $data->ministryCode);
    }

    public function test_ministry_code_can_be_null(): void
    {
        $data = new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESBSC',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );

        $this->assertNull($data->ministryCode);
    }

    public function test_rejects_empty_institution_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CampusCreateData(
            institutionId: '',
            institutionShortCode: 'IESBSC',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_invalid_institution_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESB',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_invalid_campus_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESBSC',
            shortCode: 'ABC123',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESBSC',
            shortCode: 'CENTRAL',
            name: '',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_address_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CampusCreateData(
            institutionId: '01JTESTINSTITUTION',
            institutionShortCode: 'IESBSC',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '',
        );
    }
}
