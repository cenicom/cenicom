<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Infrastructure\Identity\CampusCodeGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CampusCodeGeneratorTest extends TestCase
{
    public function test_generates_expected_code(): void
    {
        $generator = new CampusCodeGenerator();

        $code = $generator->generate(
            'IESBSC',
            'SBBXYZ',
            1,
        );

        $this->assertSame(
            'CEN-IESBSC-SBBXYZ-001',
            $code,
        );
    }

    public function test_accepts_maximum_sequence_of_999(): void
    {
        $generator = new CampusCodeGenerator();

        $code = $generator->generate(
            'IESBSC',
            'SBBXYZ',
            999,
        );

        $this->assertSame(
            'CEN-IESBSC-SBBXYZ-999',
            $code,
        );
    }

    public function test_rejects_sequence_of_1000(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Campus code sequence must be between 1 and 999.'
        );

        $generator->generate(
            'IESBSC',
            'SBBXYZ',
            1000,
        );
    }

    public function test_rejects_invalid_institution_short_code(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);

        $generator->generate(
            'IESB',
            'SBBXYZ',
            1,
        );
    }

    public function test_rejects_invalid_campus_short_code(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);

        $generator->generate(
            'IESBSC',
            'SBB',
            1,
        );
    }

    public function test_rejects_zero_sequence(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);

        $generator->generate(
            'IESBSC',
            'SBBXYZ',
            0,
        );
    }

    public function test_rejects_negative_sequence(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);

        $generator->generate(
            'IESBSC',
            'SBBXYZ',
            -1,
        );
    }
}
