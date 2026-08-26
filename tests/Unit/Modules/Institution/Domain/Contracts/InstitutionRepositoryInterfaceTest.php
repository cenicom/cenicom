<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution;
use App\Core\Contracts\RepositoryInterface;
use Tests\TestCase;

final class InstitutionRepositoryInterfaceTest extends TestCase
{
    public function test_repository_contract_declares_save_operation(): void
    {
        $reflection = new \ReflectionClass(
            InstitutionRepositoryInterface::class
        );

        $this->assertTrue(
            $reflection->hasMethod('save')
        );

        $method = $reflection->getMethod('save');

        $this->assertSame(
            Institution::class,
            (string) $method->getParameters()[0]
                ->getType()
        );

        $this->assertSame(
            Institution::class,
            (string) $method->getReturnType()
        );
    }

    public function test_repository_contract_extends_core_repository_contract(): void
    {
        $reflection = new \ReflectionClass(
            InstitutionRepositoryInterface::class
        );

        $this->assertTrue(
            $reflection->implementsInterface(
                RepositoryInterface::class
            )
        );
    }
}
