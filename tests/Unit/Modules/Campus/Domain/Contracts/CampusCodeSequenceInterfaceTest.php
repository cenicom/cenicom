<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Contracts;

use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CampusCodeSequenceInterfaceTest extends TestCase
{
    public function test_contract_declares_next_method(): void
    {
        $method = new ReflectionMethod(
            CampusCodeSequenceInterface::class,
            'next'
        );

        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
    }
}
