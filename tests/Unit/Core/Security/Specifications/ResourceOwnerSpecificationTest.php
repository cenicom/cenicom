<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\OwnedResourceInterface;
use App\Core\Security\Specifications\ResourceOwnerSpecification;
use Tests\TestCase;

final class ResourceOwnerSpecificationTest extends TestCase
{
    public function test_allows_resource_owned_by_identity(): void
    {
        $identity = $this->identity(10);

        $resource = $this->resource(10);

        $specification = new ResourceOwnerSpecification();

        self::assertTrue(
            $specification->isSatisfiedBy(
                $identity,
                $resource,
            )
        );
    }

    public function test_denies_resource_owned_by_another_identity(): void
    {
        $identity = $this->identity(10);

        $resource = $this->resource(20);

        $specification = new ResourceOwnerSpecification();

        self::assertFalse(
            $specification->isSatisfiedBy(
                $identity,
                $resource,
            )
        );
    }

    public function test_denies_resource_without_ownership_contract(): void
    {
        $identity = $this->identity(10);

        $resource = new \stdClass();

        $specification = new ResourceOwnerSpecification();

        self::assertFalse(
            $specification->isSatisfiedBy(
                $identity,
                $resource,
            )
        );
    }

    public function test_denies_identity_without_identifier(): void
    {
        $identity = $this->identity(null);

        $resource = $this->resource(10);

        $specification = new ResourceOwnerSpecification();

        self::assertFalse(
            $specification->isSatisfiedBy(
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

