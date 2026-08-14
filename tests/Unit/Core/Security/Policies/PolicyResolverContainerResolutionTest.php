<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use App\Core\Security\Policies\PolicyResolver;
use Tests\TestCase;

final class PolicyResolverContainerResolutionTest extends TestCase
{
    public function test_policy_resolver_interface_resolves(): void
    {
        $resolver = $this->app->make(
            PolicyResolverInterface::class
        );

        self::assertInstanceOf(
            PolicyResolver::class,
            $resolver
        );
    }

    public function test_policy_resolver_is_singleton(): void
    {
        $first = $this->app->make(
            PolicyResolverInterface::class
        );

        $second = $this->app->make(
            PolicyResolverInterface::class
        );

        self::assertSame(
            $first,
            $second
        );
    }
}
