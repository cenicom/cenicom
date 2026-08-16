<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionNamedType;

final class PermissionDefinitionRegistryContractTest extends TestCase
{
    public function test_permission_definition_registry_interface_exists(): void
    {
        self::assertTrue(
            interface_exists(
                PermissionDefinitionRegistryInterface::class
            )
        );
    }

    public function test_add_method_has_expected_signature(): void
    {
        $method = new ReflectionMethod(
            PermissionDefinitionRegistryInterface::class,
            'add'
        );

        $returnType = $method->getReturnType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $returnType
        );

        self::assertSame('void', $returnType->getName());
        self::assertCount(1, $method->getParameters());

        $parameter = $method->getParameters()[0];

        self::assertSame('definition', $parameter->getName());

        $parameterType = $parameter->getType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $parameterType
        );

        self::assertSame('string', $parameterType->getName());
    }

    public function test_definitions_method_has_expected_signature(): void
    {
        $method = new ReflectionMethod(
            PermissionDefinitionRegistryInterface::class,
            'definitions'
        );

        $returnType = $method->getReturnType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $returnType
        );

        self::assertSame('array', $returnType->getName());
        self::assertCount(0, $method->getParameters());
    }

    public function test_clear_method_has_expected_signature(): void
    {
        $method = new ReflectionMethod(
            PermissionDefinitionRegistryInterface::class,
            'clear'
        );

        $returnType = $method->getReturnType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $returnType
        );

        self::assertSame('void', $returnType->getName());
        self::assertCount(0, $method->getParameters());
    }
}
