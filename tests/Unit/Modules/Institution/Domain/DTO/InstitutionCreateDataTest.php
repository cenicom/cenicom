<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\DTO;

use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;
use Tests\TestCase;

final class InstitutionCreateDataTest extends TestCase
{
    public function test_creates_data_with_valid_name_and_short_code(): void
    {
        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'IESBSC',
        );

        $this->assertSame(
            'Institución Educativa Nacional Simón Bolívar',
            $data->name
        );

        $this->assertSame(
            'IESBSC',
            $data->shortCode
        );
    }

    public function test_exposes_name(): void
    {
        $data = new InstitutionCreateData(
            name: 'Escuela Batalla de Boyacá',
            shortCode: 'EBBSCN',
        );

        $this->assertSame(
            'Escuela Batalla de Boyacá',
            $data->name
        );
    }

    public function test_exposes_short_code(): void
    {
        $data = new InstitutionCreateData(
            name: 'Escuela Batalla de Boyacá',
            shortCode: 'EBBSCN',
        );

        $this->assertSame(
            'EBBSCN',
            $data->shortCode
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: '',
            shortCode: 'IESBSC',
        );
    }

    public function test_rejects_whitespace_only_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: '   ',
            shortCode: 'IIESBSC',
        );
    }

    public function test_rejects_empty_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: '',
        );
    }

    public function test_rejects_whitespace_only_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: '   ',
        );
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
            shortCode: 'IESBSC',
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
            shortCode: 'IESBSC',
        );

        $this->assertNull(
            $data->officialRegistration
        );
    }

    public function test_accepts_short_code_with_minimum_length(): void
    {
        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEF',
        );

        $this->assertSame('ABCDEF', $data->shortCode);
    }

    public function test_accepts_short_code_with_maximum_length(): void
    {
        $data = new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEFGHIJKLMNO',
        );

        $this->assertSame('ABCDEFGHIJKLMNO', $data->shortCode);
    }

    public function test_rejects_short_code_shorter_than_six_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDE',
        );
    }

    public function test_rejects_short_code_longer_than_fifteen_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEFGHIJKLMNOP',
        );
    }

    public function test_rejects_lowercase_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'abcdef',
        );
    }

    public function test_rejects_numeric_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEF1',
        );
    }

    public function test_rejects_hyphen_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEF-G',
        );
    }

    public function test_rejects_underscore_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEF_G',
        );
    }

    public function test_rejects_internal_space_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABC DEF',
        );
    }

    public function test_rejects_special_character_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionCreateData(
            name: 'Institución Educativa Nacional Simón Bolívar',
            shortCode: 'ABCDEF@',
        );
    }
}
