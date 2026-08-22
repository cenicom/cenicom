<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\Services;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Services\InstitutionCreator;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use Tests\TestCase;

final class InstitutionCreatorTest extends TestCase
{
    public function test_creates_institution_with_generated_id_and_code(): void
    {
        $expectedId = '01K2F8K4X7Q9M3N5P6R8T1W2YZ';
        $expectedCode = 'CEN-000001';

        $idGenerator = $this->createMock(
            InstitutionIdGeneratorInterface::class
        );

        $idGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($expectedId);

        $codeGenerator = $this->createMock(
            InstitutionCodeGeneratorInterface::class
        );

        $codeGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($expectedCode);

        $creator = new InstitutionCreator(
            $idGenerator,
            $codeGenerator,
        );

        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
        );

        $institution = $creator->create($data);

        $this->assertSame(
            $expectedId,
            $institution->id()
        );

        $this->assertSame(
            $expectedCode,
            $institution->code()
        );

        $this->assertSame(
            'Institución Educativa Nacional Simón Bolívar',
            $institution->name()
        );

        $this->assertSame(
            'draft',
            $institution->status()
        );
    }


    public function test_creates_institution_with_official_registration(): void
    {
        $expectedId = '01K2F8K4X7Q9M3N5P6R8T1W2YZ';
        $expectedCode = 'CEN-000002';

        $officialRegistration = new InstitutionOfficialRegistration(
            authority: 'Ministerio de Educación',
            country: 'CO',
            value: 'DUE-123456789',
        );

        $idGenerator = $this->createMock(
            InstitutionIdGeneratorInterface::class
        );

        $idGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($expectedId);

        $codeGenerator = $this->createMock(
            InstitutionCodeGeneratorInterface::class
        );

        $codeGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($expectedCode);

        $creator = new InstitutionCreator(
            $idGenerator,
            $codeGenerator,
        );

        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            officialRegistration: $officialRegistration,
        );

        $institution = $creator->create($data);

        $this->assertSame(
            $officialRegistration,
            $institution->officialRegistration()
        );
    }
}
