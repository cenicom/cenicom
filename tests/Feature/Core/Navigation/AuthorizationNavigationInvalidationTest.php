<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Authorization\Contracts\AuthorizationAssignmentServiceInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthorizationNavigationInvalidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(
            NavigationCacheInterface::class
        )->clear();
    }

    /**
     * Un cambio de roles de un usuario invalida
     * únicamente la navegación de ese usuario.
     */
    public function test_assigning_role_invalidates_user_navigation_cache(): void
    {
        $user = $this->createUser();

        $role = $this->createRole(
            name: 'administrator',
            label: 'Administrator'
        );

        $cache = app(
            NavigationCacheInterface::class
        );

        $key = NavigationCacheKey::user(
            $user->getAuthIdentifier()
        );

        $cache->put(
            $key,
            new NavigationTreeData()
        );

        $this->assertNotNull(
            $cache->get($key)
        );

        app(
            AuthorizationAssignmentServiceInterface::class
        )->assignRole(
            $user,
            $role
        );

        $this->assertNull(
            $cache->get($key)
        );
    }

    /**
     * Un permiso directo concedido al usuario
     * invalida su navegación.
     */
    public function test_granting_direct_permission_invalidates_user_navigation_cache(): void
    {
        $user = $this->createUser();

        $permission = $this->createPermission(
            'reports.view'
        );

        $cache = app(
            NavigationCacheInterface::class
        );

        $key = NavigationCacheKey::user(
            $user->getAuthIdentifier()
        );

        $cache->put(
            $key,
            new NavigationTreeData()
        );

        $this->assertNotNull(
            $cache->get($key)
        );

        app(
            AuthorizationAssignmentServiceInterface::class
        )->grantPermission(
            $user,
            $permission
        );

        $this->assertNull(
            $cache->get($key)
        );
    }

    /**
     * Un cambio de permisos sobre un rol
     * invalida la navegación globalmente.
     */
    public function test_changing_role_permission_invalidates_navigation_cache_globally(): void
    {
        $administrator = $this->createUser(
            userCode: 'USR-ADMIN',
            email: 'administrator@test.local'
        );

        $manager = $this->createUser(
            userCode: 'USR-MANAGER',
            email: 'manager@test.local'
        );

        $role = $this->createRole(
            name: 'administrator',
            label: 'Administrator'
        );

        $permission = $this->createPermission(
            'reports.view'
        );

        $cache = app(
            NavigationCacheInterface::class
        );

        $administratorKey = NavigationCacheKey::user(
            $administrator->getAuthIdentifier()
        );

        $managerKey = NavigationCacheKey::user(
            $manager->getAuthIdentifier()
        );

        $cache->put(
            $administratorKey,
            new NavigationTreeData()
        );

        $cache->put(
            $managerKey,
            new NavigationTreeData()
        );

        $this->assertNotNull(
            $cache->get($administratorKey)
        );

        $this->assertNotNull(
            $cache->get($managerKey)
        );

        app(
            AuthorizationAssignmentServiceInterface::class
        )->grantPermissionToRole(
            $role,
            $permission
        );

        $this->assertNull(
            $cache->get($administratorKey)
        );

        $this->assertNull(
            $cache->get($managerKey)
        );
    }

    /**
     * Revocar un permiso de un rol también
     * invalida globalmente la navegación.
     */
    public function test_revoking_role_permission_invalidates_navigation_cache_globally(): void
    {
        $role = $this->createRole(
            name: 'manager',
            label: 'Manager'
        );

        $permission = $this->createPermission(
            'reports.view'
        );

        $role->permissions()->attach(
            $permission->getKey()
        );

        $cache = app(
            NavigationCacheInterface::class
        );

        $key = NavigationCacheKey::user(100);

        $cache->put(
            $key,
            new NavigationTreeData()
        );

        $this->assertNotNull(
            $cache->get($key)
        );

        app(
            AuthorizationAssignmentServiceInterface::class
        )->revokePermissionFromRole(
            $role,
            $permission
        );

        $this->assertNull(
            $cache->get($key)
        );
    }

    /**
     * El servicio debe estar correctamente registrado
     * en el contenedor.
     */
    public function test_assignment_service_is_resolvable(): void
    {
        $service = app(
            AuthorizationAssignmentServiceInterface::class
        );

        $this->assertInstanceOf(
            AuthorizationAssignmentServiceInterface::class,
            $service
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fixtures
    |--------------------------------------------------------------------------
    */

    private function createUser(
        string $userCode = 'USR-' . 'AUTH-' . '001',
        string $email = 'authorization-' . '001' . '@test.local'
    ): User {
        $unique = str_replace(
            '.',
            '',
            uniqid('', true)
        );

        return User::query()->create([
            'user_code' => $userCode . '-' . $unique,
            'user_name' => 'authorization-' . $unique,
            'first_name' => 'Authorization',
            'last_name' => 'Test',
            'email' => $email . '.' . $unique,
            'password' => Hash::make(
                'password-' . $unique
            ),
        ]);
    }

    private function createRole(
        string $name,
        string $label
    ): Role {
        return Role::query()->create([
            'name' => $name,
            'label' => $label,
        ]);
    }

    private function createPermission(
        string $name
    ): Permission {
        return Permission::query()->create([
            'name' => $name,
            'description' => 'Permission used by feature tests.',
            'module' => 'Test',
        ]);
    }
}
