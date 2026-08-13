<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionNamedType;

final class PermissionDefinitionInterfaceTest extends TestCase
{
    public function test_permission_definition_interface_exists(): void
    {
        self::assertTrue(
            interface_exists(PermissionDefinitionInterface::class)
        );
    }

    public function test_permission_definition_contract_declares_register_method(): void
    {
        self::assertTrue(
            method_exists(
                PermissionDefinitionInterface::class,
                'register'
            )
        );
    }

    public function test_register_method_accepts_permission_registrar(): void
    {
        $method = new ReflectionMethod(
            PermissionDefinitionInterface::class,
            'register'
        );

        $parameters = $method->getParameters();

        self::assertCount(1, $parameters);

        $parameterType = $parameters[0]->getType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $parameterType
        );

        self::assertSame(
            PermissionRegistrarInterface::class,
            $parameterType->getName()
        );
    }

    public function test_register_method_returns_void(): void
    {
        $method = new ReflectionMethod(
            PermissionDefinitionInterface::class,
            'register'
        );

        $returnType = $method->getReturnType();

        self::assertInstanceOf(
            ReflectionNamedType::class,
            $returnType
        );

        self::assertSame(
            'void',
            $returnType->getName()
        );
    }
}
