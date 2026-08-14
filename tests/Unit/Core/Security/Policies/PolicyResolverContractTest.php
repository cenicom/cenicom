<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use Tests\TestCase;

final class PolicyResolverContractTest extends TestCase
{
    public function test_contract_can_resolve_policy(): void
    {
        $policy = $this->policy();

        $resolver = $this->resolver($policy);

        self::assertSame(
            $policy,
            $resolver->resolve('institution'),
        );
    }

    public function test_contract_returns_null_for_unknown_policy(): void
    {
        $resolver = $this->resolver(
            $this->policy(),
        );

        self::assertNull(
            $resolver->resolve('unknown'),
        );
    }

    private function resolver(
        PolicyInterface $policy
    ): PolicyResolverInterface {
        return new class ($policy) implements PolicyResolverInterface {
            public function __construct(
                private readonly PolicyInterface $policy,
            ) {
            }

            public function resolve(
                string $name
            ): ?PolicyInterface {
                return $name === 'institution'
                    ? $this->policy
                    : null;
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
