<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Contracts;

use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class CampusCreatorInterfaceTest extends TestCase
{
    public function test_contract_declares_create_method(): void
    {
        $this->assertTrue(
            method_exists(CampusCreatorInterface::class, 'create')
        );

        $method = new ReflectionMethod(
            CampusCreatorInterface::class,
            'create'
        );

        $this->assertTrue($method->isPublic());
    }
}
