<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Address\Domain\DTO;

use App\Modules\Address\Domain\DTOs\GeographicOption;
use PHPUnit\Framework\TestCase;

final class GeographicOptionTest extends TestCase
{
    public function test_it_exposes_id_and_name(): void
    {
        $option = new GeographicOption(
            id: 57,
            name: 'Huila',
        );

        self::assertSame(57, $option->id);
        self::assertSame('Huila', $option->name);
    }
}
