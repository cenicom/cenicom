<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Authorization;

use App\Core\Navigation\Authorization\NavigationPermissionResolver;
use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use PHPUnit\Framework\TestCase;

final class NavigationPermissionIntegrationTest extends TestCase
{
    public function test_guest_identity_sees_only_public_navigation(): void
    {
        $identity = $this->createIdentity(
            permissions: []
        );

        $tree = $this->buildNavigation($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(1, $children);

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );
    }

    public function test_partial_permissions_show_only_allowed_items(): void
    {
        $identity = $this->createIdentity(
            permissions: [
                'institutions.view',
            ]
        );

        $tree = $this->buildNavigation($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(2, $children);

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );

        $this->assertSame(
            'institutions',
            $children[1]->id()
        );
    }

    public function test_admin_identity_sees_all_navigation_items(): void
    {
        $identity = $this->createIdentity(
            permissions: [
                'institutions.view',
                'users.view',
                'roles.view',
            ]
        );

        $tree = $this->buildNavigation($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(4, $children);
    }

    private function buildNavigation(
        IdentityInterface $identity
    ) {
        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 1,
        );

        $items = [
            new NavigationItemData(
                id: 'dashboard',
                label: 'Dashboard',
                route: 'dashboard',
                permission: null,
                group: 'administration',
                order: 1,
            ),
            new NavigationItemData(
                id: 'institutions',
                label: 'Instituciones',
                route: 'institutions.index',
                permission: 'institutions.view',
                group: 'administration',
                order: 2,
            ),
            new NavigationItemData(
                id: 'users',
                label: 'Usuarios',
                route: 'users.index',
                permission: 'users.view',
                group: 'administration',
                order: 3,
            ),
            new NavigationItemData(
                id: 'roles',
                label: 'Roles',
                route: 'roles.index',
                permission: 'roles.view',
                group: 'administration',
                order: 4,
            ),
        ];

        $registry
            ->method('groups')
            ->willReturn([
                'administration' => $group,
            ]);

        $registry
            ->method('items')
            ->willReturn($items);

        $authorization = new NavigationPermissionResolver(
            $this->createAuthorization()
        );

        return (new NavigationBuilder(
            $registry,
            $authorization,
        ))->build($identity);
    }

    private function createAuthorization(): AuthorizationServiceInterface
    {
        return new class implements AuthorizationServiceInterface {
            public function can(
                IdentityInterface $identity,
                string $permission
            ): bool {
                return in_array(
                    $permission,
                    $identity->permissions(),
                    true
                );
            }

            public function allows(
                IdentityInterface $identity,
                string $policy,
                mixed $resource
            ): bool {
                return false;
            }
        };
    }

    private function createIdentity(
        array $permissions
    ): IdentityInterface {
        return new class($permissions) implements IdentityInterface {
            public function __construct(
                private array $permissions
            ) {}

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
                return true;
            }
        };
    }
}
