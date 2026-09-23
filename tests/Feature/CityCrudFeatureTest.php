<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\City\Models\City;
use App\Modules\Country\Models\Country;
use App\Modules\State\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CityCrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_city_can_be_created_through_http(): void
    {
        fwrite(STDERR, "\n>>> ENTER CITY CREATE TEST\n");

        fwrite(STDERR, ">>> BEFORE USER\n");

        $user = User::factory()->create();

        fwrite(STDERR, ">>> AFTER USER\n");

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        fwrite(STDERR, ">>> AFTER COUNTRY\n");

        $state = State::create([
            'name' => 'Huila',
            'country_id' => $country->getKey(),
        ]);

        fwrite(STDERR, ">>> AFTER STATE\n");

        $response = $this
            ->actingAs($user)
            ->post(route('cities.store'), [
                'name' => 'Neiva',
                'state_id' => $state->getKey(),
            ]);

        fwrite(STDERR, ">>> AFTER POST\n");

        $response->assertRedirect();

        fwrite(STDERR, ">>> AFTER REDIRECT ASSERT\n");

        $this->assertDatabaseHas('cities', [
            'name' => 'Neiva',
            'state_id' => $state->getKey(),
        ]);

        fwrite(STDERR, ">>> TEST END\n");
    }

    public function test_city_can_be_updated_through_http(): void
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

        $city = City::create([
            'name' => 'Neiva',
            'state_id' => $state->getKey(),
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('cities.update', $city),
                [
                    'name' => 'Neiva actualizado',
                    'state_id' => $state->getKey(),
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('cities', [
            'id' => $city->getKey(),
            'name' => 'Neiva actualizado',
            'state_id' => $state->getKey(),
        ]);
    }

    public function test_city_can_be_deleted_through_http(): void
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

        $city = City::create([
            'name' => 'Neiva',
            'state_id' => $state->getKey(),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route('cities.destroy', $city)
            );

        $response->assertRedirect();

        $this->assertDatabaseMissing('cities', [
            'id' => $city->getKey(),
        ]);
    }
}
