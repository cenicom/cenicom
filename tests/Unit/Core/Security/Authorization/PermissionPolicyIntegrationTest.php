<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;


use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\SpecificationPolicy;
use App\Core\Security\Specifications\PermissionSpecification;
use Tests\TestCase;

final class PermissionPolicyIntegrationTest extends TestCase
{
    public function test_permission_specification_can_be_used_as_policy(): void
    {
        $identity = new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Test User';
            }

            public function roles(): array
            {
                return [];
            }

            public function permissions(): array
            {
                return [
                    'institutions.view',
                ];
            }

            public function can(
                string $permission
            ): bool {
                return in_array(
                    $permission,
                    $this->permissions(),
                    true
                );
            }

            public function authenticated(): bool
            {
                return true;
            }
        };

        $policy = new SpecificationPolicy(
            new PermissionSpecification(
                'institutions.view'
            )
        );

        $this->assertTrue(
            $policy->allows($identity, null)
        );
    }

    public function test_permission_policy_denies_missing_permission(): void
    {
        $identity = new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Test User';
            }

            public function roles(): array
            {
                return [];
            }

            public function permissions(): array
            {
                return [];
            }

            public function can(
                string $permission
            ): bool {
                return false;
            }

            public function authenticated(): bool
            {
                return true;
            }
        };

        $policy = new SpecificationPolicy(
            new PermissionSpecification(
                'institutions.view'
            )
        );

        $this->assertFalse(
            $policy->allows($identity, null)
        );
    }
}


