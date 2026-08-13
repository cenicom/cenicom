<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\PolicyRegistry;
use Tests\TestCase;

final class PolicyRegistryTest extends TestCase
{
    public function test_registers_and_resolves_policy(): void
    {
        $registry = new PolicyRegistry();

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

    public function test_returns_all_registered_policies(): void
    {
        $registry = new PolicyRegistry();

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

    public function test_returns_null_for_unknown_policy(): void
    {
        $registry = new PolicyRegistry();

        self::assertNull(
            $registry->policy('unknown'),
        );
    }

    public function test_replacing_existing_policy_is_idempotent_by_key(): void
    {
        $registry = new PolicyRegistry();

        $first = $this->policy();
        $second = $this->policy();

        $registry->register('institution', $first);
        $registry->register('institution', $second);

        self::assertSame(
            $second,
            $registry->policy('institution'),
        );

        self::assertCount(
            1,
            $registry->all(),
        );
    }

    public function test_clear_removes_all_policies(): void
    {
        $registry = new PolicyRegistry();

        $registry->register(
            'institution',
            $this->policy(),
        );

        $registry->clear();

        self::assertSame(
            [],
            $registry->all(),
        );
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
