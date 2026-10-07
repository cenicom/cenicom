<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Contracts;

use App\Modules\Campus\Domain\Contracts\CampusIdGeneratorInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CampusIdGeneratorInterfaceTest extends TestCase
{
    public function test_contract_declares_generate_method(): void
    {
        $method = new ReflectionMethod(
            CampusIdGeneratorInterface::class,
            'generate'
        );

        $this->assertTrue($method->isPublic());
    }
}
