<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCodeGeneratorInterface;
use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use App\Modules\Campus\Domain\Contracts\CampusIdGeneratorInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Services\CampusCreator;
use PHPUnit\Framework\TestCase;

final class CampusCreatorTest extends TestCase
{
    public function test_creates_campus_with_generated_identity_and_code(): void
    {
        $idGenerator = $this->createMock(CampusIdGeneratorInterface::class);
        $sequence = $this->createMock(CampusCodeSequenceInterface::class);
        $codeGenerator = $this->createMock(CampusCodeGeneratorInterface::class);

        $idGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('01JTESTCAMPUS00000000000001');

        $sequence
            ->expects($this->once())
            ->method('next')
            ->with('institution-01')
            ->willReturn(1);

        $codeGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'IESBSC',
                'SBBXYZ',
                1,
            )
            ->willReturn('CEN-IESBSC-SBBXYZ-001');

        $creator = new CampusCreator(
            $idGenerator,
            $sequence,
            $codeGenerator,
        );

        $campus = $creator->create(
            new CampusCreateData(
                institutionId: 'institution-01',
                institutionShortCode: 'IESBSC',
                shortCode: 'SBBXYZ',
                name: 'Campus Principal',
                addressId: 'address-01',
                ministryCode: null,
            )
        );

        $this->assertSame(
            '01JTESTCAMPUS00000000000001',
            $campus->id()
        );
        $this->assertSame(
            'institution-01',
            $campus->institutionId()
        );
        $this->assertSame(
            'CEN-IESBSC-SBBXYZ-001',
            $campus->code()
        );
        $this->assertSame(
            'SBBXYZ',
            $campus->shortCode()
        );
        $this->assertSame(
            'Campus Principal',
            $campus->name()
        );
        $this->assertSame(
            'address-01',
            $campus->addressId()
        );
        $this->assertNull(
            $campus->ministryCode()
        );
        $this->assertSame(
            'draft',
            $campus->status()
        );
    }

    public function test_creates_campus_with_ministry_code(): void
    {
        $idGenerator = $this->createMock(CampusIdGeneratorInterface::class);
        $sequence = $this->createMock(CampusCodeSequenceInterface::class);
        $codeGenerator = $this->createMock(CampusCodeGeneratorInterface::class);

        $idGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('01JTESTCAMPUS00000000000002');

        $sequence
            ->expects($this->once())
            ->method('next')
            ->with('institution-01')
            ->willReturn(2);

        $codeGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'IESBSC',
                'SBBXYZ',
                2,
            )
            ->willReturn('CEN-IESBSC-SBBXYZ-002');

        $creator = new CampusCreator(
            $idGenerator,
            $sequence,
            $codeGenerator,
        );

        $campus = $creator->create(
            new CampusCreateData(
                institutionId: 'institution-01',
                institutionShortCode: 'IESBSC',
                shortCode: 'SBBXYZ',
                name: 'Campus Principal',
                addressId: 'address-01',
                ministryCode: 'MIN-001',
            )
        );

        $this->assertSame(
            '01JTESTCAMPUS00000000000002',
            $campus->id()
        );
        $this->assertSame(
            'institution-01',
            $campus->institutionId()
        );
        $this->assertSame(
            'CEN-IESBSC-SBBXYZ-002',
            $campus->code()
        );
        $this->assertSame(
            'SBBXYZ',
            $campus->shortCode()
        );
        $this->assertSame(
            'Campus Principal',
            $campus->name()
        );
        $this->assertSame(
            'address-01',
            $campus->addressId()
        );
        $this->assertSame(
            'MIN-001',
            $campus->ministryCode()
        );
        $this->assertSame(
            'draft',
            $campus->status()
        );
    }
}
