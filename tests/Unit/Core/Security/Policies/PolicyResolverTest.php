<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\PolicyResolver;
use Tests\TestCase;

final class PolicyResolverTest extends TestCase
{
    public function test_resolves_registered_policy(): void
    {
        $registry = $this->registry();

        $policy = $this->policy();

        $registry->register(
            'institution',
            $policy,
        );

        $resolver = new PolicyResolver($registry);

        self::assertSame(
            $policy,
            $resolver->resolve('institution'),
        );
    }

    public function test_returns_null_for_unknown_policy(): void
    {
        $registry = $this->registry();

        $resolver = new PolicyResolver($registry);

        self::assertNull(
            $resolver->resolve('unknown'),
        );
    }

    public function test_delegates_resolution_to_registry(): void
    {
        $registry = $this->createMock(
            PolicyRegistryInterface::class
        );

        $policy = $this->policy();

        $registry
            ->expects(self::once())
            ->method('policy')
            ->with('institution')
            ->willReturn($policy);

        $resolver = new PolicyResolver($registry);

        self::assertSame(
            $policy,
            $resolver->resolve('institution'),
        );
    }

    private function registry(): PolicyRegistryInterface
    {
        return new class implements PolicyRegistryInterface {
            /**
             * @var array<string, PolicyInterface>
             */
            private array $policies = [];

            public function register(
                string $name,
                PolicyInterface $policy
            ): void {
                $this->policies[$name] = $policy;
            }

            public function policy(
                string $name
            ): ?PolicyInterface {
                return $this->policies[$name] ?? null;
            }

            public function all(): array
            {
                return $this->policies;
            }

            public function clear(): void
            {
                $this->policies = [];
            }
        };
    }

    private function policy(): PolicyInterface
    {
        return new class implements PolicyInterface {
            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return true;
            }
        };
    }
}
