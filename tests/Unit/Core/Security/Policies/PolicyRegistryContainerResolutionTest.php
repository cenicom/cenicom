<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\PolicyRegistry;
use Tests\TestCase;

final class PolicyRegistryContainerResolutionTest extends TestCase
{
    public function test_policy_registry_interface_resolves(): void
    {
        $registry = $this->app->make(
            PolicyRegistryInterface::class
        );

        self::assertInstanceOf(
            PolicyRegistry::class,
            $registry
        );
    }

    public function test_policy_registry_is_singleton(): void
    {
        $first = $this->app->make(
            PolicyRegistryInterface::class
        );

        $second = $this->app->make(
            PolicyRegistryInterface::class
        );

        self::assertSame(
            $first,
            $second
        );
    }
}
