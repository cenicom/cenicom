<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;
use Tests\TestCase;

final class NavigationSecurityEndToEndTest extends TestCase
{
    protected function tearDown(): void
    {
        app(RoleRegistryInterface::class)->clear();

        app(NavigationRegistryInterface::class)->clear();

        parent::tearDown();
    }

    public function test_authorized_identity_sees_institution_navigation(): void
    {
        $roles = app(RoleRegistryInterface::class);

        $roles->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrador',
                permissions: [
                    'institutions.view',
                ],
            )
        );

        $identity = $this->identity(
            roles: ['administrator'],
        );

        $tree = app(
            NavigationBuilderInterface::class
        )->build($identity);

        $institutions = $this->findNode(
            $tree->nodes(),
            'institutions',
        );

        $this->assertNotNull($institutions);

        $this->assertSame(
            'Instituciones',
            $institutions->label()
        );

        $this->assertSame(
            'institutions.index',
            $institutions->route()
        );
    }

    public function test_unauthorized_identity_does_not_see_institution_navigation(): void
    {
        $roles = app(RoleRegistryInterface::class);

        $roles->register(
            new RoleDefinition(
                name: 'viewer',
                label: 'Viewer',
                permissions: [],
            )
        );

        $identity = $this->identity(
            roles: ['viewer'],
        );

        $tree = app(
            NavigationBuilderInterface::class
        )->build($identity);

        $institutions = $this->findNode(
            $tree->nodes(),
            'institutions',
        );

        $this->assertNull($institutions);
    }

    public function test_direct_permission_also_authorizes_institution_navigation(): void
    {
        $identity = $this->identity(
            permissions: [
                'institutions.view',
            ],
        );

        $tree = app(
            NavigationBuilderInterface::class
        )->build($identity);

        $institutions = $this->findNode(
            $tree->nodes(),
            'institutions',
        );

        $this->assertNotNull($institutions);
    }

    public function test_guest_does_not_see_protected_institution_navigation(): void
    {
        $identity = $this->identity(
            authenticated: false,
        );

        $tree = app(
            NavigationBuilderInterface::class
        )->build($identity);

        $institutions = $this->findNode(
            $tree->nodes(),
            'institutions',
        );

        $this->assertNull($institutions);
    }

    /**
     * @param array<int, object> $nodes
     */
    private function findNode(
        array $nodes,
        string $id,
    ): ?object {
        foreach ($nodes as $node) {
            if ($node->id() === $id) {
                return $node;
            }

            $found = $this->findNode(
                $node->children(),
                $id,
            );

            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    private function identity(
        array $roles = [],
        array $permissions = [],
        bool $authenticated = true,
    ): IdentityInterface {
        return new class(
            $roles,
            $permissions,
            $authenticated,
        ) implements IdentityInterface {
            /**
             * @param array<int, string> $roles
             * @param array<int, string> $permissions
             */
            public function __construct(
                private array $roles,
                private array $permissions,
                private bool $authenticated,
            ) {
            }

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
                return $this->roles;
            }

            public function permissions(): array
            {
                return $this->permissions;
            }

            public function can(
                string $permission
            ): bool {
                return in_array(
                    $permission,
                    $this->permissions,
                    true
                );
            }

            public function authenticated(): bool
            {
                return $this->authenticated;
            }
        };
    }
}
