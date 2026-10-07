<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Infrastructure\Identity\CampusIdGenerator;
use Tests\TestCase;

final class CampusIdGeneratorTest extends TestCase
{
    public function test_generates_string_id(): void
    {
        $generator = new CampusIdGenerator();

        $id = $generator->generate();

        $this->assertIsString($id);
    }

    public function test_generates_valid_ulid(): void
    {
        $generator = new CampusIdGenerator();

        $id = $generator->generate();

        $this->assertTrue(
            preg_match(
                '/^[0-9ABCDEFGHJKMNPQRSTVWXYZ]{26}$/',
                $id
            ) === 1
        );
    }

    public function test_generates_ulid_with_expected_length(): void
    {
        $generator = new CampusIdGenerator();

        $id = $generator->generate();

        $this->assertSame(26, strlen($id));
    }

    public function test_generates_different_ids(): void
    {
        $generator = new CampusIdGenerator();

        $first = $generator->generate();
        $second = $generator->generate();

        $this->assertNotSame($first, $second);
    }
}
