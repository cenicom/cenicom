<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use Tests\TestCase;

final class PolicyRegistryContractTest extends TestCase
{
    public function test_contract_can_register_and_resolve_policy(): void
    {
        $registry = $this->registry();

        $policy = $this->policy();

        $registry->register(
            'institution',
            $policy,
        );

        self::assertSame(
            $policy,
            $registry->policy('institution'),
        );
    }

    public function test_contract_returns_all_registered_policies(): void
    {
        $registry = $this->registry();

        $first = $this->policy();
        $second = $this->policy();

        $registry->register('first', $first);
        $registry->register('second', $second);

        self::assertSame(
            [
                'first' => $first,
                'second' => $second,
            ],
            $registry->all(),
        );
    }

    public function test_contract_returns_null_for_unknown_policy(): void
    {
        $registry = $this->registry();

        self::assertNull(
            $registry->policy('unknown'),
        );
    }

    public function test_contract_can_clear_registry(): void
    {
        $registry = $this->registry();

        $registry->register(
            'test',
            $this->policy(),
        );

        $registry->clear();

        self::assertSame(
            [],
            $registry->all(),
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
