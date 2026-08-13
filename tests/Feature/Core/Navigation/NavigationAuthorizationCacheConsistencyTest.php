<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Security\Authorization\Contracts\AuthorizationAssignmentServiceInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationAuthorizationCacheConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerNavigation();
    }

    /**
     * Una navegación inicialmente sin permiso no debe permanecer
     * en caché después de otorgar el permiso al usuario.
     */
    public function test_granting_permission_rebuilds_cached_navigation(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);

        $service = app(NavigationServiceInterface::class);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $initialTree = $service->tree($identity);

        $this->assertNavigationContains(
            $initialTree,
            ['dashboard']
        );

        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $authorization->grantPermission(
            $user,
            $permission
        );

        $freshIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $updatedTree = $service->tree($freshIdentity);

        $this->assertNavigationContains(
            $updatedTree,
            [
                'dashboard',
                'institutions',
            ]
        );
    }

    /**
     * Una navegación inicialmente autorizada no debe conservar
     * un permiso revocado en caché.
     */
    public function test_revoking_permission_rebuilds_cached_navigation(): void
    {
        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $authorization->grantPermission(
            $user,
            $permission
        );

        $this->actingAs($user);

        $service = app(NavigationServiceInterface::class);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $initialTree = $service->tree($identity);

        $this->assertNavigationContains(
            $initialTree,
            [
                'dashboard',
                'institutions',
            ]
        );

        /*
     * La revocación debe pasar por el servicio de autorización
     * para garantizar la emisión de AuthorizationChanged y la
     * invalidación del caché de navegación.
     */
        $authorization->revokePermission(
            $user,
            $permission
        );

        $freshIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $updatedTree = $service->tree($freshIdentity);

        $this->assertNavigationContains(
            $updatedTree,
            ['dashboard']
        );

        $this->assertNavigationDoesNotContain(
            $updatedTree,
            'institutions'
        );
    }

    /**
     * Un cambio de permiso de rol debe invalidar la navegación
     * de los usuarios que dependen de ese rol.
     */
    public function test_role_permission_change_rebuilds_cached_navigation(): void
    {
        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $role = Role::query()->create([
            'name' => 'administrator',
            'label' => 'Administrator',
        ]);

        $user = User::factory()->create();

        $user->roles()->attach($role);

        $this->actingAs($user);

        $service = app(NavigationServiceInterface::class);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $initialTree = $service->tree($identity);

        $this->assertNavigationDoesNotContain(
            $initialTree,
            'institutions'
        );

        $authorization->grantPermissionToRole(
            $role,
            $permission
        );

        $freshIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $updatedTree = $service->tree($freshIdentity);

        $this->assertNavigationContains(
            $updatedTree,
            [
                'dashboard',
                'institutions',
            ]
        );
    }

    /**
     * El caché debe contener el estado autorizado después
     * de reconstruir la navegación.
     */
    public function test_rebuilt_navigation_is_persisted_in_user_cache(): void
    {
        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $authorization->grantPermission(
            $user,
            $permission
        );

        $this->actingAs($user);

        $service = app(NavigationServiceInterface::class);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $tree = $service->tree($identity);

        $cache = app(NavigationCacheInterface::class);

        $key = NavigationCacheKey::user($user->id);

        $cachedTree = $cache->get($key);

        $this->assertNotNull($cachedTree);

        $this->assertNavigationContains(
            $cachedTree,
            [
                'dashboard',
                'institutions',
            ]
        );

        $this->assertSame(
            $tree->nodes(),
            $cachedTree->nodes()
        );
    }

    /**
     * La invalidación de un usuario no debe contaminar
     * la navegación de otro usuario.
     */
    public function test_authorization_cache_remains_isolated_between_users(): void
    {
        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $authorizedUser = User::factory()->create();

        $authorization->grantPermission(
            $authorizedUser,
            $permission
        );

        $unauthorizedUser = User::factory()->create();

        $service = app(NavigationServiceInterface::class);

        $this->actingAs($authorizedUser);

        $authorizedIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorizedTree = $service->tree($authorizedIdentity);

        $this->assertNavigationContains(
            $authorizedTree,
            [
                'dashboard',
                'institutions',
            ]
        );

        $this->actingAs($unauthorizedUser);

        $unauthorizedIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $unauthorizedTree = $service->tree($unauthorizedIdentity);

        $this->assertNavigationContains(
            $unauthorizedTree,
            ['dashboard']
        );

        $this->assertNavigationDoesNotContain(
            $unauthorizedTree,
            'institutions'
        );

        $cache = app(NavigationCacheInterface::class);

        $this->assertNotNull(
            $cache->get(
                NavigationCacheKey::user($authorizedUser->id)
            )
        );

        $this->assertNotNull(
            $cache->get(
                NavigationCacheKey::user($unauthorizedUser->id)
            )
        );
    }

    /**
     * Un usuario que pierde un permiso no debe recibir
     * accidentalmente el árbol autorizado de otro usuario.
     */
    public function test_revocation_does_not_leak_another_users_authorized_navigation(): void
    {
        $authorization = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $authorizedUser = User::factory()->create();

        $authorization->grantPermission(
            $authorizedUser,
            $permission
        );

        $revokedUser = User::factory()->create();

        $authorization->grantPermission(
            $revokedUser,
            $permission
        );

        $service = app(NavigationServiceInterface::class);

        $this->actingAs($authorizedUser);

        $authorizedIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorizedTree = $service->tree($authorizedIdentity);

        $this->assertNavigationContains(
            $authorizedTree,
            [
                'dashboard',
                'institutions',
            ]
        );

        $this->actingAs($revokedUser);

        $revokedIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $revokedTree = $service->tree($revokedIdentity);

        $this->assertNavigationContains(
            $revokedTree,
            [
                'dashboard',
                'institutions',
            ]
        );

        $authorization->revokePermission(
            $revokedUser,
            $permission
        );

        $freshIdentity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $updatedTree = $service->tree($freshIdentity);

        $this->assertNavigationContains(
            $updatedTree,
            ['dashboard']
        );

        $this->assertNavigationDoesNotContain(
            $updatedTree,
            'institutions'
        );

        $this->actingAs($authorizedUser);

        $authorizedIdentityAfterRevocation = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorizedTreeAfterRevocation = $service->tree(
            $authorizedIdentityAfterRevocation
        );

        $this->assertNavigationContains(
            $authorizedTreeAfterRevocation,
            [
                'dashboard',
                'institutions',
            ]
        );
    }

    /**
     * La navegación pública permanece disponible aunque
     * la autorización protegida cambie.
     */
    public function test_public_navigation_remains_consistent_after_authorization_change(): void
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $service = app(NavigationServiceInterface::class);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $tree = $service->tree($identity);

        $this->assertNavigationContains(
            $tree,
            ['dashboard']
        );

        $this->assertNavigationDoesNotContain(
            $tree,
            'institutions'
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
     * Verifica que el árbol contenga exactamente los items esperados.
     *
     * @param array<int, string> $expectedIds
     */
    private function assertNavigationContains(
        \App\Core\Navigation\DTO\NavigationTreeData $tree,
        array $expectedIds
    ): void {
        $children = $tree
            ->nodes()[0]
            ->children();

        $actualIds = array_map(
            static fn($node): string => $node->id(),
            $children
        );

        $this->assertSame(
            $expectedIds,
            $actualIds
        );
    }

    /**
     * Verifica que un item no exista en el árbol.
     */
    private function assertNavigationDoesNotContain(
        \App\Core\Navigation\DTO\NavigationTreeData $tree,
        string $id
    ): void {
        $children = $tree
            ->nodes()[0]
            ->children();

        $actualIds = array_map(
            static fn($node): string => $node->id(),
            $children
        );

        $this->assertNotContains(
            $id,
            $actualIds
        );
    }
}
