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
            $code
        );
    }

    public function test_sequence_can_exceed_three_digits(): void
    {
        $generator = new CampusCodeGenerator();

        $code = $generator->generate(
            'IESBSC',
            'SBBXYZ',
            1000,
        );

        $this->assertSame(
            'CEN-IESBSC-SBBXYZ-1000',
            $code
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

    public function test_rejects_non_positive_sequence(): void
    {
        $generator = new CampusCodeGenerator();

        $this->expectException(InvalidArgumentException::class);

        $generator->generate(
            'IESBSC',
            'SBBXYZ',
            0,
        );
    }
}
