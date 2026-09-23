<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Country\Models\Country;
use App\Modules\State\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StateCrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_state_can_be_created_through_http(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('states.store'), [
                'name' => 'Huila',
                'country_id' => $country->getKey(),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('states', [
            'name' => 'Huila',
            'country_id' => $country->getKey(),
        ]);
    }

    public function test_state_can_be_updated_through_http(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $state = State::create([
            'name' => 'Huila',
            'country_id' => $country->getKey(),
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('states.update', $state),
                [
                    'name' => 'Huila actualizado',
                    'country_id' => $country->getKey(),
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('states', [
            'id' => $state->getKey(),
            'name' => 'Huila actualizado',
            'country_id' => $country->getKey(),
        ]);
    }

    public function test_state_can_be_deleted_through_http(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $state = State::create([
            'name' => 'Huila',
            'country_id' => $country->getKey(),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route('states.destroy', $state)
            );

        $response->assertRedirect();

        $this->assertDatabaseMissing('states', [
            'id' => $state->getKey(),
        ]);
    }
}
