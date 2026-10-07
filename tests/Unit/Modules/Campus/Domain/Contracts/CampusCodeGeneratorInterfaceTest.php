<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Contracts;

use App\Modules\Campus\Domain\Contracts\CampusCodeGeneratorInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CampusCodeGeneratorInterfaceTest extends TestCase
{
    public function test_contract_declares_generate_method(): void
    {
        $method = new ReflectionMethod(
            CampusCodeGeneratorInterface::class,
            'generate'
        );

        $this->assertTrue($method->isPublic());
        $this->assertCount(3, $method->getParameters());
    }
}
