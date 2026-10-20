<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class PermissionCatalogSyncCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_declared_permissions(): void
    {
        $this->artisan('cenicom:permissions-sync')
            ->assertSuccessful();

        $this->assertDatabaseCount('permissions', 11);

        $this->assertDatabaseHas('permissions', [
            'name' => 'institutions.view',
            'description' => 'Permite consultar instituciones.',
            'module' => 'institution',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'campuses.view',
            'description' => 'Permite consultar sedes educativas.',
            'module' => 'campus',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'inventory.products.view',
            'description' => 'Permite consultar productos.',
            'module' => 'inventory',
        ]);
    }

    public function test_repeated_synchronization_does_not_duplicate_permissions(): void
    {
        $this->artisan('cenicom:permissions-sync')
            ->assertSuccessful();

        $this->artisan('cenicom:permissions-sync')
            ->assertSuccessful();

        $this->assertDatabaseCount('permissions', 11);

        $this->assertSame(
            11,
            Permission::query()->distinct('name')->count('name')
        );
    }

    public function test_it_updates_metadata_of_existing_permissions(): void
    {
        Permission::query()->create([
            'name' => 'institutions.view',
            'description' => 'Descripción anterior.',
            'module' => 'legacy',
        ]);

        $this->artisan('cenicom:permissions-sync')
            ->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'institutions.view',
            'description' => 'Permite consultar instituciones.',
            'module' => 'institution',
        ]);

        $this->assertDatabaseCount('permissions', 11);
    }

    public function test_synchronization_does_not_delete_existing_permissions(): void
    {
        Permission::query()->create([
            'name' => 'legacy.custom',
            'description' => 'Permiso heredado.',
            'module' => 'legacy',
        ]);

        $this->artisan('cenicom:permissions-sync')
            ->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'legacy.custom',
            'description' => 'Permiso heredado.',
            'module' => 'legacy',
        ]);

        $this->assertDatabaseCount('permissions', 12);
    }
}
