<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\DTO;

use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;
use Tests\TestCase;

final class InstitutionCreateDataTest extends TestCase
{
    public function test_creates_data_with_valid_name(): void
    {
        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
        );

        $this->assertSame(
            'Institución Educativa Nacional Simón Bolívar',
            $data->name
        );
    }

    public function test_exposes_name(): void
    {
        $data = new InstitutionCreateData(
            name: 'Escuela Batalla de Boyacá',
        );

        $this->assertSame(
            'Escuela Batalla de Boyacá',
            $data->name
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(name: '');
    }

    public function test_rejects_whitespace_only_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(name: '   ');
    }

    public function test_accepts_official_registration(): void
    {
        $registration = new InstitutionOfficialRegistration(
            country: 'CO',
            authority: 'Education Authority',
            value: '123456789',
        );

        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            officialRegistration: $registration,
        );

        $this->assertSame(
            $registration,
            $data->officialRegistration
        );
    }

    public function test_official_registration_is_optional(): void
    {
        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
        );

        $this->assertNull(
            $data->officialRegistration
        );
    }
}
