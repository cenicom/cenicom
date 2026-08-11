<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Identity\Identity;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Tests\TestCase;

final class SecurityIdentityBindingTest extends TestCase
{
    public function test_identity_interface_resolves_from_container(): void
    {
        $identity = $this->app->make(
            IdentityInterface::class
        );

        $this->assertInstanceOf(
            IdentityInterface::class,
            $identity
        );
    }


    public function test_identity_interface_resolves_as_identity(): void
    {
        $identity = $this->app->make(
            IdentityInterface::class
        );

        $this->assertInstanceOf(
            Identity::class,
            $identity
        );
    }


    public function test_identity_interface_uses_current_security_identity(): void
    {
        $user = new SecurityTestUser(
            id: 10,
            name: 'Administrator',
            roles: ['admin'],
            permissions: [
                'institutions.view',
                'users.view',
            ],
        );

        $this->app->instance(
            AuthFactory::class,
            new SecurityTestAuthFactory(
                new SecurityTestGuard($user)
            )
        );

        $identity = $this->app->make(
            IdentityInterface::class
        );

        $this->assertSame(
            10,
            $identity->id()
        );

        $this->assertSame(
            'Administrator',
            $identity->name()
        );

        $this->assertSame(
            ['admin'],
            $identity->roles()
        );

        $this->assertSame(
            [
                'institutions.view',
                'users.view',
            ],
            $identity->permissions()
        );

        $this->assertTrue(
            $identity->authenticated()
        );
    }
}


/**
 * Fake Auth Factory para integración del binding.
 */
final class SecurityTestAuthFactory implements AuthFactory
{
    public function __construct(
        private readonly SecurityTestGuard $guard
    ) {
    }

    public function guard($name = null)
    {
        return $this->guard;
    }

    public function shouldUse($name)
    {
        return $this;
    }
}


/**
 * Fake Guard.
 */
final class SecurityTestGuard
{
    public function __construct(
        private readonly ?SecurityTestUser $user = null
    ) {
    }

    public function user(): ?SecurityTestUser
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }
}


/**
 * Fake User.
 */
final class SecurityTestUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        private readonly array $roles = [],
        private readonly array $permissions = [],
    ) {
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function roles(): SecurityTestRelation
    {
        return new SecurityTestRelation($this->roles);
    }

    public function permissions(): SecurityTestRelation
    {
        return new SecurityTestRelation($this->permissions);
    }
}


/**
 * Fake relación Eloquent mínima.
 */
final class SecurityTestRelation
{
    public function __construct(
        private readonly array $names
    ) {
    }

    public function pluck(string $column): SecurityTestCollection
    {
        return new SecurityTestCollection($this->names);
    }
}


final class SecurityTestCollection
{
    public function __construct(
        private readonly array $values
    ) {
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
