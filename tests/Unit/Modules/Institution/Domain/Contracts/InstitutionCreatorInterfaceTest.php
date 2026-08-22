<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Entity\Institution;
use ReflectionMethod;
use Tests\TestCase;

final class InstitutionCreatorInterfaceTest extends TestCase
{
    public function test_create_method_exists(): void
    {
        $method = new ReflectionMethod(
            InstitutionCreatorInterface::class,
            'create'
        );

        $this->assertTrue($method->isPublic());
    }

    public function test_create_accepts_institution_create_data(): void
    {
        $method = new ReflectionMethod(
            InstitutionCreatorInterface::class,
            'create'
        );

        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);

        $this->assertSame(
            InstitutionCreateData::class,
            $parameters[0]->getType()->getName()
        );
    }

    public function test_create_returns_institution(): void
    {
        $method = new ReflectionMethod(
            InstitutionCreatorInterface::class,
            'create'
        );

        $this->assertSame(
            Institution::class,
            $method->getReturnType()->getName()
        );
    }
}
