<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\OwnedResourceInterface;
use App\Core\Security\Policies\ResourceSpecificationPolicy;
use App\Core\Security\Specifications\ResourceOwnerSpecification;
use Tests\TestCase;

final class ResourceOwnerPolicyIntegrationTest extends TestCase
{
    public function test_policy_allows_identity_to_access_owned_resource(): void
    {
        $identity = $this->identity(10);

        $resource = $this->resource(10);

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    public function test_policy_denies_identity_to_access_resource_owned_by_another_identity(): void
    {
        $identity = $this->identity(10);

        $resource = $this->resource(20);

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    private function identity(
        int|string|null $id
    ): IdentityInterface {
        return new class($id) implements IdentityInterface {
            public function __construct(
                private readonly int|string|null $identityId
            ) {
            }

            public function id(): int|string|null
            {
                return $this->identityId;
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
    }

    private function resource(
        int|string|null $ownerId
    ): OwnedResourceInterface {
        return new class($ownerId) implements OwnedResourceInterface {
            public function __construct(
                private readonly int|string|null $owner
            ) {
            }

            public function ownerId(): int|string|null
            {
                return $this->owner;
            }
        };
    }
}
