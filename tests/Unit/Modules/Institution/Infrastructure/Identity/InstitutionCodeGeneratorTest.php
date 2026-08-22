<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Infrastructure\Identity;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use App\Modules\Institution\Infrastructure\Identity\InstitutionCodeGenerator;
use InvalidArgumentException;
use Tests\TestCase;

final class InstitutionCodeGeneratorTest extends TestCase
{
    public function test_generates_code_from_sequence(): void
    {
        $sequence = $this->createMock(
            InstitutionCodeSequenceInterface::class
        );

        $sequence
            ->expects($this->once())
            ->method('next')
            ->willReturn(1);

        $generator = new InstitutionCodeGenerator($sequence);

        $this->assertSame(
            'CEN-000001',
            $generator->generate()
        );
    }

    public function test_formats_sequence_with_six_digits(): void
    {
        $sequence = $this->createMock(
            InstitutionCodeSequenceInterface::class
        );

        $sequence
            ->expects($this->once())
            ->method('next')
            ->willReturn(42);

        $generator = new InstitutionCodeGenerator($sequence);

        $this->assertSame(
            'CEN-000042',
            $generator->generate()
        );
    }

    public function test_preserves_large_sequence_values(): void
    {
        $sequence = $this->createMock(
            InstitutionCodeSequenceInterface::class
        );

        $sequence
            ->expects($this->once())
            ->method('next')
            ->willReturn(1000000);

        $generator = new InstitutionCodeGenerator($sequence);

        $this->assertSame(
            'CEN-1000000',
            $generator->generate()
        );
    }

    public function test_rejects_zero_sequence(): void
    {
        $sequence = $this->createMock(
            InstitutionCodeSequenceInterface::class
        );

        $sequence
            ->expects($this->once())
            ->method('next')
            ->willReturn(0);

        $generator = new InstitutionCodeGenerator($sequence);

        $this->expectException(InvalidArgumentException::class);

        $generator->generate();
    }

    public function test_rejects_negative_sequence(): void
    {
        $sequence = $this->createMock(
            InstitutionCodeSequenceInterface::class
        );

        $sequence
            ->expects($this->once())
            ->method('next')
            ->willReturn(-1);

        $generator = new InstitutionCodeGenerator($sequence);

        $this->expectException(InvalidArgumentException::class);

        $generator->generate();
    }
}
