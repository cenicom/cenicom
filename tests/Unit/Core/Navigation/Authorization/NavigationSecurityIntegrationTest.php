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
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;
use Tests\TestCase;

final class NavigationSecurityIntegrationTest extends TestCase
{
    protected function tearDown(): void
    {
        app(RoleRegistryInterface::class)->clear();

        parent::tearDown();
    }

    public function test_navigation_uses_real_security_permission_resolution(): void
    {
        $roleRegistry = app(
            RoleRegistryInterface::class
        );

        $roleRegistry->register(
            new RoleDefinition(
                'admin',
                'Administrator',
                [
                    'institutions.view',
                ]
            )
        );

        $identity = $this->identity(
            roles: ['admin']
        );

        $tree = $this->buildNavigation(
            $identity
        );

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(
            2,
            $children
        );

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );

        $this->assertSame(
            'institutions',
            $children[1]->id()
        );
    }

    public function test_navigation_hides_item_when_real_security_denies_permission(): void
    {
        $roleRegistry = app(
            RoleRegistryInterface::class
        );

        $roleRegistry->register(
            new RoleDefinition(
                'viewer',
                'Viewer',
                []
            )
        );

        $identity = $this->identity(
            roles: ['viewer']
        );

        $tree = $this->buildNavigation(
            $identity
        );

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(
            1,
            $children
        );

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );
    }

    private function buildNavigation(
        IdentityInterface $identity
    ) {
        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $registry
            ->method('groups')
            ->willReturn([
                'administration' => new NavigationGroupData(
                    id: 'administration',
                    label: 'Administración',
                    icon: 'bi-gear',
                    order: 1,
                ),
            ]);

        $registry
            ->method('items')
            ->willReturn([
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
            ]);

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $permissionResolver = new NavigationPermissionResolver(
            $authorization
        );

        return (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build(
            $identity
        );
    }

    private function identity(
        array $roles
    ): IdentityInterface {
        return new class($roles) implements IdentityInterface {
            public function __construct(
                private array $roles
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
                return $this->roles;
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
}
