<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\RepositoryInterface;

use App\Core\Generator\Builders\RepositoryInterfaceBuilder;
use Tests\Support\GeneratorTestCase;

final class RepositoryInterfaceBuilderTest extends GeneratorTestCase
{
    public function test_build_generates_repository_interface_variables(): void
    {
        $builder = new RepositoryInterfaceBuilder();

        $module = $this->createModuleData();

        $variables = $builder->build($module);

        self::assertSame(
            $module->repositoryContractNamespace(),
            $variables['namespace']
        );

        self::assertSame(
            $module->repositoryInterface(),
            $variables['repositoryInterface']
        );

        self::assertSame(
            $module->qualifiedModel(),
            $variables['qualifiedModel']
        );

        self::assertSame(
            $module->modelClass(),
            $variables['model']
        );

        self::assertSame(
            $module->variable(),
            $variables['variable']
        );
    }
}
