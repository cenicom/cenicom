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
    }

    public function test_rejects_empty_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
        );
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: '',
            code: 'CEN-000001',
        );
    }

    public function test_rejects_empty_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: '',
        );
    }

    public function test_status_defaults_to_draft(): void
    {
        $institution = new Institution(
            id: '01K2F8K4X7Q9M3N5P6R8T1W2YZ',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
        );

        $this->assertSame(
            'draft',
            $institution->status()
        );
    }
}
