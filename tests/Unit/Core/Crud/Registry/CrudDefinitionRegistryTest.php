<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Registry;

use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class CrudDefinitionRegistryTest extends TestCase
{
    public function test_registry_implements_contract(): void
    {
        $registry = new CrudDefinitionRegistry();

        $this->assertInstanceOf(
            CrudDefinitionRegistryInterface::class,
            $registry
        );
    }

    public function test_add_registers_definition(): void
    {
        $registry = new CrudDefinitionRegistry();

        $definition =
            'Tests\\Fixtures\\Crud\\TestCrudDefinition';

        $registry->add($definition);

        $this->assertSame(
            [$definition],
            $registry->definitions()
        );
    }

    public function test_add_registers_multiple_definitions(): void
    {
        $registry = new CrudDefinitionRegistry();

        $first =
            'Tests\\Fixtures\\Crud\\FirstCrudDefinition';

        $second =
            'Tests\\Fixtures\\Crud\\SecondCrudDefinition';

        $registry->add($first);
        $registry->add($second);

        $this->assertSame(
            [$first, $second],
            $registry->definitions()
        );
    }

    public function test_definitions_returns_empty_array_when_registry_is_empty(): void
    {
        $registry = new CrudDefinitionRegistry();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }

    public function test_clear_removes_definitions(): void
    {
        $registry = new CrudDefinitionRegistry();

        $registry->add(
            'Tests\\Fixtures\\Crud\\TestCrudDefinition'
        );

        $registry->clear();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }
}
