<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Security\Contracts\IdentityInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationSecurityPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerNavigation();
    }

    /**
     * Un permiso persistido mediante un rol
     * permite visualizar el item de navegación.
     */
    public function test_user_sees_navigation_item_from_persisted_role_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $role = Role::query()->create([
            'name' => 'administrator',
            'label' => 'Administrator',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();

        $user->roles()->attach($role);

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

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

    /**
     * Un permiso persistido directamente sobre el usuario
     * permite visualizar el item de navegación.
     */
    public function test_user_sees_navigation_item_from_direct_persisted_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

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

    /**
     * Un usuario autenticado sin el permiso
     * no puede visualizar el item protegido.
     */
    public function test_user_without_permission_cannot_see_protected_navigation_item(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(1, $children);

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );
    }

    /**
     * Un usuario con múltiples roles recibe
     * la unión de permisos de sus roles.
     */
    public function test_user_with_multiple_roles_sees_items_from_all_roles(): void
    {
        $institutionsPermission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $usersPermission = Permission::query()->create([
            'name' => 'users.view',
        ]);

        $administrator = Role::query()->create([
            'name' => 'administrator',
            'label' => 'Administrator',
        ]);

        $administrator->permissions()->attach(
            $institutionsPermission
        );

        $manager = Role::query()->create([
            'name' => 'manager',
            'label' => 'Manager',
        ]);

        $manager->permissions()->attach(
            $usersPermission
        );

        $user = User::factory()->create();

        $user->roles()->attach([
            $administrator->id,
            $manager->id,
        ]);

        $this->registerAdditionalItem(
            id: 'users',
            label: 'Usuarios',
            permission: 'users.view',
            order: 3,
        );

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(3, $children);

        $this->assertSame(
            [
                'dashboard',
                'institutions',
                'users',
            ],
            array_map(
                static fn ($node) => $node->id(),
                $children
            )
        );
    }

    /**
     * Un usuario invitado solamente puede visualizar
     * navegación pública.
     */
    public function test_guest_sees_only_public_navigation(): void
    {
        $this->assertGuest();

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(1, $children);

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );
    }

    /**
     * Un grupo cuyos elementos están todos protegidos
     * desaparece cuando ninguno está autorizado.
     */
    public function test_group_without_authorized_children_is_removed(): void
    {
        $registry = app(NavigationRegistryInterface::class);

        $registry->clear();

        $registry->registerGroup(
            new NavigationGroupData(
                id: 'administration',
                label: 'Administración',
                icon: 'bi-gear',
                order: 1,
            )
        );

        $registry->registerItem(
            new NavigationItemData(
                id: 'institutions',
                label: 'Instituciones',
                icon: 'bi-building',
                route: 'institutions.index',
                permission: 'institutions.view',
                group: 'administration',
                order: 1,
            )
        );

        $user = User::factory()->create();

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

        $nodes = $tree->nodes();

        $this->assertCount(0, $nodes);
    }

    /**
     * Los elementos públicos permanecen visibles
     * aunque no exista ningún permiso asociado.
     */
    public function test_public_navigation_item_is_visible_without_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $tree = app(NavigationServiceInterface::class)
            ->tree($identity);

        $children = $tree
            ->nodes()[0]
            ->children();

        $this->assertCount(1, $children);

        $this->assertSame(
            'dashboard',
            $children[0]->id()
        );
    }

    /**
     * Registra la estructura base de navegación.
     */
    private function registerNavigation(): void
    {
        $registry = app(NavigationRegistryInterface::class);

        $registry->clear();

        $registry->registerGroup(
            new NavigationGroupData(
                id: 'administration',
                label: 'Administración',
                icon: 'bi-gear',
                order: 1,
            )
        );

        $registry->registerItem(
            new NavigationItemData(
                id: 'dashboard',
                label: 'Dashboard',
                icon: 'bi-speedometer2',
                route: 'dashboard',
                permission: null,
                group: 'administration',
                order: 1,
            )
        );

        $registry->registerItem(
            new NavigationItemData(
                id: 'institutions',
                label: 'Instituciones',
                icon: 'bi-building',
                route: 'institutions.index',
                permission: 'institutions.view',
                group: 'administration',
                order: 2,
            )
        );
    }

    /**
     * Registra un item adicional.
     */
    private function registerAdditionalItem(
        string $id,
        string $label,
        string $permission,
        int $order,
    ): void {
        app(NavigationRegistryInterface::class)->registerItem(
            new NavigationItemData(
                id: $id,
                label: $label,
                icon: 'bi-person',
                route: $id . '.index',
                permission: $permission,
                group: 'administration',
                order: $order,
            )
        );
    }
}

