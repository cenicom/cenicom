<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CampusRepositoryInterfaceTest extends TestCase
{
    public function test_repository_contract_declares_save_operation(): void
    {
        $reflection = new ReflectionClass(CampusRepositoryInterface::class);

        $this->assertTrue(
            $reflection->hasMethod('save')
        );

        $method = $reflection->getMethod('save');

        $this->assertSame(
            Campus::class,
            (string) $method->getParameters()[0]->getType()
        );

        $this->assertSame(
            Campus::class,
            (string) $method->getReturnType()
        );
    }

    public function test_repository_contract_extends_core_repository_contract(): void
    {
        $reflection = new ReflectionClass(CampusRepositoryInterface::class);

        $this->assertTrue(
            $reflection->implementsInterface(RepositoryInterface::class)
        );
    }
}
