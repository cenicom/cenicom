<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\Entity;

use App\Modules\Institution\Domain\Entity\Institution;
use InvalidArgumentException;
use Tests\TestCase;

final class InstitutionTest extends TestCase
{
    public function test_creates_institution_with_valid_data(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );

        $this->assertSame(
            '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            $institution->id()
        );

        $this->assertSame(
            'Institución Educativa Nacional Simón Bolívar',
            $institution->name()
        );

        $this->assertSame(
            'CEN-000001',
            $institution->code()
        );

        $this->assertSame(
            'IESBSC',
            $institution->shortCode()
        );
    }

    public function test_rejects_empty_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: '',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );
    }

    public function test_rejects_empty_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: '',
            shortCode: 'IESBSC',
        );
    }

    public function test_rejects_empty_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: '',
        );
    }

    public function test_status_defaults_to_draft(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );

        $this->assertSame(
            'draft',
            $institution->status()
        );
    }

    public function test_accepts_short_code_with_minimum_length(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEF',
        );

        $this->assertSame(
            'ABCDEF',
            $institution->shortCode()
        );
    }

    public function test_accepts_short_code_with_maximum_length(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEFGHIJKLMNO',
        );

        $this->assertSame(
            'ABCDEFGHIJKLMNO',
            $institution->shortCode()
        );
    }

    public function test_rejects_short_code_shorter_than_six_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDE',
        );
    }

    public function test_rejects_short_code_longer_than_fifteen_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEFGHIJKLMNOP',
        );
    }

    public function test_rejects_lowercase_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'abcdef',
        );
    }

    public function test_rejects_numeric_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEF1',
        );
    }

    public function test_rejects_hyphen_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEF-G',
        );
    }

    public function test_rejects_underscore_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEF_G',
        );
    }

    public function test_rejects_internal_space_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABC DEF',
        );
    }

    public function test_rejects_special_character_in_short_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'ABCDEF@',
        );
    }

    public function test_rejects_unknown_status(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
            status: 'suspended',
        );
    }

    public function test_draft_institution_can_be_activated_without_mutating_original(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );

        $activated = $institution->activate();

        $this->assertSame('draft', $institution->status());
        $this->assertSame('active', $activated->status());
        $this->assertNotSame($institution, $activated);
        $this->assertSame($institution->id(), $activated->id());
        $this->assertSame($institution->name(), $activated->name());
        $this->assertSame($institution->code(), $activated->code());
        $this->assertSame($institution->shortCode(), $activated->shortCode());
        $this->assertSame(
            $institution->officialRegistration(),
            $activated->officialRegistration()
        );
    }

    public function test_inactive_institution_can_be_activated_again(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
            status: 'inactive',
        );

        $this->assertSame('active', $institution->activate()->status());
        $this->assertSame('inactive', $institution->status());
    }

    public function test_active_institution_can_be_deactivated(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
            status: 'active',
        );

        $inactive = $institution->deactivate();

        $this->assertSame('inactive', $inactive->status());
        $this->assertSame('active', $institution->status());
    }

    public function test_draft_institution_cannot_be_deactivated(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
        );

        try {
            $institution->deactivate();
            $this->fail('Expected an invalid transition to be rejected.');
        } catch (InvalidArgumentException) {
            $this->assertSame('draft', $institution->status());
        }
    }

    public function test_active_institution_cannot_be_activated_again(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
            shortCode: 'IESBSC',
            status: 'active',
        );

        $this->expectException(InvalidArgumentException::class);

        $institution->activate();
    }
}
