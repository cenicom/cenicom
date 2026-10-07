<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Entity;

use App\Modules\Campus\Domain\Entity\Campus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CampusTest extends TestCase
{
    public function test_creates_valid_campus(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
            ministryCode: 'MIN-001',
        );

        $this->assertSame('01JTESTCAMPUS', $campus->id());
        $this->assertSame('01JTESTINSTITUTION', $campus->institutionId());
        $this->assertSame('CEN-IESBSC-CENTRAL-001', $campus->code());
        $this->assertSame('CENTRAL', $campus->shortCode());
        $this->assertSame('Campus Central', $campus->name());
        $this->assertSame('01JTESTADDRESS', $campus->addressId());
        $this->assertSame('MIN-001', $campus->ministryCode());
        $this->assertSame('draft', $campus->status());
    }

    public function test_ministry_code_can_be_null(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );

        $this->assertNull($campus->ministryCode());
    }

    public function test_default_status_is_draft(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );

        $this->assertSame('draft', $campus->status());
    }

    public function test_draft_campus_can_be_activated(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );

        $campus->activate();

        $this->assertSame('active', $campus->status());
    }

    public function test_active_campus_can_be_deactivated(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
            status: 'active',
        );

        $campus->deactivate();

        $this->assertSame('inactive', $campus->status());
    }

    public function test_inactive_campus_can_be_activated_again(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
            status: 'inactive',
        );

        $campus->activate();

        $this->assertSame('active', $campus->status());
    }

    public function test_draft_campus_cannot_be_deactivated_directly(): void
    {
        $campus = new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );

        $this->expectException(InvalidArgumentException::class);

        $campus->deactivate();
    }

    public function test_rejects_empty_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_institution_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: '',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_invalid_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-ABC123-001',
            shortCode: 'ABC123',
            name: 'Campus Central',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: '',
            addressId: '01JTESTADDRESS',
        );
    }

    public function test_rejects_empty_address_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Campus(
            id: '01JTESTCAMPUS',
            institutionId: '01JTESTINSTITUTION',
            code: 'CEN-IESBSC-CENTRAL-001',
            shortCode: 'CENTRAL',
            name: 'Campus Central',
            addressId: '',
        );
    }
}
