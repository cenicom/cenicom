<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Country\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CountryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_can_be_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('countries.index'));

        $response->assertOk();
    }

    public function test_create_page_can_be_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('countries.create'));

        $response->assertOk();
    }

    public function test_show_page_can_be_displayed(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('countries.show', $country));

        $response
            ->assertOk()
            ->assertSee('Detalle del país')
            ->assertSee('Nombre')
            ->assertSee('Código ISO 2')
            ->assertSee('Código ISO 3')
            ->assertSee('Colombia')
            ->assertSee('CO')
            ->assertSee('COL');
    }

    public function test_edit_page_can_be_displayed(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('countries.edit', $country));

        $response
            ->assertOk()
            ->assertSee('Países')
            ->assertSee('Editar país')
            ->assertSee('Colombia')
            ->assertSee('CO')
            ->assertSee('COL');
    }
}
