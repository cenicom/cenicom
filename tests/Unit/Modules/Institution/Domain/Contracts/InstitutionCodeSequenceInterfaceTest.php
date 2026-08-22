<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use Tests\TestCase;

final class InstitutionCodeSequenceInterfaceTest extends TestCase
{
    public function test_sequence_returns_next_integer(): void
    {
        $sequence = new class implements InstitutionCodeSequenceInterface {
            private int $value = 0;

            public function next(): int
            {
                return ++$this->value;
            }
        };

        $this->assertSame(1, $sequence->next());
        $this->assertSame(2, $sequence->next());
        $this->assertSame(3, $sequence->next());
    }
}
