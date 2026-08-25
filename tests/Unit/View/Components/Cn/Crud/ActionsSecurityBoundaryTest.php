<?php

declare(strict_types=1);

namespace Tests\Unit\View\Components\Cn\Crud;

use App\View\Components\Cn\Crud\Actions;
use ReflectionClass;
use Tests\TestCase;

final class ActionsSecurityBoundaryTest extends TestCase
{
    public function test_component_does_not_depend_on_security_contracts(): void
    {
        $reflection = new ReflectionClass(Actions::class);

        foreach ($reflection->getConstructor()?->getParameters() ?? [] as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                continue;
            }

            self::assertNotSame(
                'App\Core\Security\Contracts\IdentityInterface',
                $type->getName(),
            );

            self::assertNotSame(
                'App\Core\Crud\Contracts\CrudActionAuthorizationInterface',
                $type->getName(),
            );
        }
    }

    public function test_component_does_not_expose_authorization_api(): void
    {
        $reflection = new ReflectionClass(Actions::class);

        self::assertFalse(
            $reflection->hasMethod('authorized'),
        );

        self::assertFalse(
            $reflection->hasMethod('allows'),
        );

        self::assertFalse(
            $reflection->hasMethod('can'),
        );
    }
}
