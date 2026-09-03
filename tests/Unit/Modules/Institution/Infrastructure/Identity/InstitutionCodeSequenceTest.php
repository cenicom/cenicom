<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Infrastructure\Identity;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use App\Modules\Institution\Infrastructure\Identity\InstitutionCodeSequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstitutionCodeSequenceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sequence_implements_contract(): void
    {
        $sequence = app(InstitutionCodeSequence::class);

        $this->assertInstanceOf(
            InstitutionCodeSequenceInterface::class,
            $sequence
        );
    }

    #[Test]
    public function first_value_is_one(): void
    {
        $sequence = app(InstitutionCodeSequence::class);

        $this->assertSame(
            1,
            $sequence->next()
        );
    }

    #[Test]
    public function successive_values_are_incremented(): void
    {
        $sequence = app(InstitutionCodeSequence::class);

        $this->assertSame(1, $sequence->next());
        $this->assertSame(2, $sequence->next());
        $this->assertSame(3, $sequence->next());
    }

    #[Test]
    public function sequence_state_is_persistent(): void
    {
        $firstSequence = app(InstitutionCodeSequence::class);

        $this->assertSame(
            1,
            $firstSequence->next()
        );

        $secondSequence = app(InstitutionCodeSequence::class);

        $this->assertSame(
            2,
            $secondSequence->next()
        );
    }

    #[Test]
    public function sequence_is_independent_from_institution_records(): void
    {
        $sequence = app(InstitutionCodeSequence::class);

        $this->assertSame(1, $sequence->next());
        $this->assertSame(2, $sequence->next());

        $this->assertDatabaseMissing('institutions', [
            'code' => 'CEN-001',
        ]);

        $this->assertDatabaseMissing('institutions', [
            'code' => 'CEN-002',
        ]);
    }
}
