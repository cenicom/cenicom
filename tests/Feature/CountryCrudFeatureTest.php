<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Country\Domain\Contracts\CountryServiceInterface;
use App\Modules\Country\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CountryCrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_country_can_be_created_through_http(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('countries.store'), [
                'name' => 'Colombia',
                'iso2' => 'CO',
                'iso3' => 'COL',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('countries', [
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);
    }

    public function test_country_can_be_updated_through_http(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('countries.update', $country),
                [
                    'name' => 'Republica de Colombia',
                    'iso2' => 'CO',
                    'iso3' => 'COL',
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'name' => 'Republica de Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);
    }

    public function test_country_service_can_update_country(): void
    {
        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $service = app(CountryServiceInterface::class);

        $result = $service->update(
            $country->getKey(),
            [
                'name' => 'Republica de Colombia',
                'iso2' => 'CO',
                'iso3' => 'COL',
            ]
        );

        $this->assertTrue($result);

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'name' => 'Republica de Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);
    }

    public function test_country_can_be_deleted_through_http(): void
    {
        $user = User::factory()->create();

        $country = Country::create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route('countries.destroy', $country)
            );

        $response->assertRedirect();

        $this->assertDatabaseMissing('countries', [
            'id' => $country->id,
        ]);
    }
}
