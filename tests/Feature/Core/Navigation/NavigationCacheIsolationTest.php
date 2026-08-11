<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class NavigationCacheIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('array')->clear();
        config(['cache.default' => 'array']);
    }

    /**
     * Un usuario no debe recibir el árbol cacheado de otro usuario.
     */
    public function test_navigation_cache_is_isolated_by_identity(): void
    {
        $service = app(NavigationServiceInterface::class);

        $userOne = $this->identity(
            id: 1,
            permissions: ['users.view']
        );

        $userTwo = $this->identity(
            id: 2,
            permissions: ['reports.view']
        );

        $treeOne = $service->tree($userOne);
        $treeTwo = $service->tree($userTwo);

        $this->assertNotSame(
            $treeOne,
            $treeTwo
        );

        $this->assertTrue(
            Cache::has(
                NavigationCacheKey::user(1)
            )
        );

        $this->assertTrue(
            Cache::has(
                NavigationCacheKey::user(2)
            )
        );
    }

    /**
     * El árbol de navegación debe reutilizarse desde caché
     * para la misma identidad.
     */
    public function test_navigation_tree_is_reused_from_cache_for_same_identity(): void
    {
        $service = app(NavigationServiceInterface::class);

        $identity = $this->identity(
            id: 10,
            permissions: ['users.view']
        );

        $firstTree = $service->tree($identity);

        $cachedTree = app(NavigationCacheInterface::class)
            ->get(
                NavigationCacheKey::user(10)
            );

        $this->assertNotNull($cachedTree);
        $this->assertSame(
            $firstTree,
            $cachedTree
        );

        $secondTree = $service->tree($identity);

        $this->assertSame(
            $firstTree,
            $secondTree
        );
    }

    /**
     * La identidad invitada utiliza una clave de caché independiente.
     */
    public function test_guest_navigation_uses_guest_cache_key(): void
    {
        $service = app(NavigationServiceInterface::class);

        $guest = $this->identity(
            id: null,
            permissions: []
        );

        $tree = $service->tree($guest);

        $cachedTree = app(NavigationCacheInterface::class)
            ->get(
                NavigationCacheKey::guest()
            );

        $this->assertNotNull($cachedTree);
        $this->assertSame(
            $tree,
            $cachedTree
        );

        $this->assertTrue(
            Cache::has(
                NavigationCacheKey::guest()
            )
        );
    }

    /**
     * La clave de un usuario autenticado no debe coincidir
     * con la clave global de invitado.
     */
    public function test_authenticated_and_guest_cache_keys_are_different(): void
    {
        $this->assertNotSame(
            NavigationCacheKey::guest(),
            NavigationCacheKey::user(1)
        );
    }

    /**
     * Verifica que limpiar una clave de usuario no afecta
     * la navegación cacheada de otro usuario.
     */
    public function test_forgetting_one_user_cache_does_not_affect_another_user(): void
    {
        $service = app(NavigationServiceInterface::class);

        $userOne = $this->identity(
            id: 100,
            permissions: ['users.view']
        );

        $userTwo = $this->identity(
            id: 200,
            permissions: ['reports.view']
        );

        $service->tree($userOne);
        $service->tree($userTwo);

        $cache = app(NavigationCacheInterface::class);

        $cache->forget(
            NavigationCacheKey::user(100)
        );

        $this->assertNull(
            $cache->get(
                NavigationCacheKey::user(100)
            )
        );

        $this->assertNotNull(
            $cache->get(
                NavigationCacheKey::user(200)
            )
        );
    }

    /**
     * Construye una identidad mínima para la prueba.
     *
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    private function identity(
        int|string|null $id,
        array $roles = [],
        array $permissions = [],
    ): IdentityInterface {
        return new class(
            $id,
            $roles,
            $permissions
        ) implements IdentityInterface {
            /**
             * @param array<int, string> $roles
             * @param array<int, string> $permissions
             */
            public function __construct(
                private readonly int|string|null $id,
                private readonly array $roles,
                private readonly array $permissions,
            ) {
            }

            public function id(): int|string|null
            {
                return $this->id;
            }

            public function name(): string
            {
                return $this->id === null
                    ? 'Guest'
                    : 'User '.$this->id;
            }

            /**
             * @return array<int, string>
             */
            public function roles(): array
            {
                return $this->roles;
            }

            /**
             * @return array<int, string>
             */
            public function permissions(): array
            {
                return $this->permissions;
            }

            public function can(string $permission): bool
            {
                return in_array(
                    $permission,
                    $this->permissions,
                    true
                );
            }

            public function authenticated(): bool
            {
                return $this->id !== null;
            }
        };
    }
}
