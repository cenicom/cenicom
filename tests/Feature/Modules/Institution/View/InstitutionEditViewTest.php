<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution\View;

use App\Models\Permission;
use App\Models\User;
use App\Modules\Institution\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class InstitutionEditViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_view_renders_persisted_institution(): void
    {
        $user = User::factory()->create([
            'user_name' => 'EDITVIEWUSER',
            'first_name' => 'Edit',
            'last_name' => 'View',
        ]);

        $permission = Permission::query()->create([
            'name' => 'institutions.update',
        ]);

        $user->permissions()->attach($permission->id);

        Auth::login($user);

        $institution = Institution::query()->create([
            'code' => 'CEN-000010',
            'short_code' => 'IECENTRAL',
            'name' => 'Institución CENICOM',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-000010',
            'status' => 'draft',
        ]);

        $response = $this->get(
            route('institutions.edit', $institution)
        );

        $response->assertOk();

        $response->assertSee('Editar institución');
        $response->assertSee('Actualización de la información de la institución educativa');

        $response->assertSee('name="name"', false);
        $response->assertSee('value="Institución CENICOM"', false);

        $response->assertSee(
            'name="officialRegistration[country]"',
            false
        );
        $response->assertSee('value="CO"', false);

        $response->assertSee(
            'name="officialRegistration[authority]"',
            false
        );
        $response->assertSee(
            'value="Ministerio de Educación"',
            false
        );

        $response->assertSee(
            'name="officialRegistration[value]"',
            false
        );
        $response->assertSee('value="REG-000010"', false);

        $response->assertSee('name="_method"', false);
        $response->assertSee('value="PUT"', false);

        $response->assertDontSee('name="shortCode"', false);
    }
}
