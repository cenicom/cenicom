<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution\View;

use App\Core\Security\Contracts\IdentityInterface;
use App\Models\Permission;
use App\Models\User;
use App\Modules\Institution\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InstitutionIndexViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renders_persisted_institution(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $this->actingAs($user);

        $identity = app(IdentityInterface::class);

        $this->assertContains(
            'institutions.view',
            $identity->permissions(),
        );

        Institution::query()->create([
            'id' => '01JTESTINSTITUTION000000001',
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route('institutions.index')
        );

        $response->assertOk();

        $response->assertViewIs('institutions::index');

        $response->assertSee(
            'Institución Educativa Simón Bolívar'
        );

        $response->assertSee('IESBSC');
    }

    public function test_index_hides_actions_without_permissions(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $this->actingAs($user);

        $institution = Institution::query()->create([
            'id' => '01JTESTINSTITUTION000000001',
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route('institutions.index')
        );

        $response->assertOk();

        $response->assertDontSee(
            route('institutions.create'),
            false,
        );

        $response->assertDontSee(
            route('institutions.edit', $institution),
            false,
        );

        $response->assertDontSee(
            route('institutions.destroy', $institution),
            false,
        );
    }

    public function test_index_shows_actions_with_permissions(): void
    {
        $permissions = [
            Permission::query()->create([
                'name' => 'institutions.view',
            ]),
            Permission::query()->create([
                'name' => 'institutions.create',
            ]),
            Permission::query()->create([
                'name' => 'institutions.update',
            ]),
            Permission::query()->create([
                'name' => 'institutions.delete',
            ]),
        ];

        $user = User::factory()->create();

        $user->permissions()->attach(
            array_map(
                static fn(Permission $permission): int => $permission->id,
                $permissions,
            ),
        );

        $this->actingAs($user);

        $institution = Institution::query()->create([
            'id' => '01JTESTINSTITUTION000000001',
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route('institutions.index')
        );

        $response->assertOk();

        $response->assertSee(
            route('institutions.create'),
            false,
        );

        $response->assertSee(
            route('institutions.edit', $institution),
            false,
        );

        $response->assertSee(
            route('institutions.destroy', $institution),
            false,
        );
    }
}
