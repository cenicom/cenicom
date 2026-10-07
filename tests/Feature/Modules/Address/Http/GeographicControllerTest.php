<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Address\Http;

use App\Models\User;
use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Address\Domain\DTOs\GeographicOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GeographicControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_countries_endpoint(): void
    {
        $response = $this->getJson('/countries');

        $response->assertRedirect('/login');
    }

    public function test_unverified_user_cannot_access_countries_endpoint(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/countries');

        $response->assertForbidden();
    }

    public function test_verified_user_receives_countries_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('countries')
                    ->once()
                    ->andReturn([
                        new GeographicOption(1, 'Colombia'),
                    ]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/countries');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function test_verified_user_receives_states_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('statesByCountry')
                    ->once()
                    ->with(1)
                    ->andReturn([
                        new GeographicOption(1, 'Huila'),
                    ]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/countries/1/states');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function test_verified_user_receives_cities_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('citiesByState')
                    ->once()
                    ->with(1)
                    ->andReturn([
                        new GeographicOption(1, 'Neiva'),
                    ]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/states/1/cities');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function test_verified_user_receives_empty_countries_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('countries')
                    ->once()
                    ->andReturn([]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/countries');

        $response
            ->assertOk()
            ->assertJson([
                'data' => [],
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function test_verified_user_receives_empty_states_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('statesByCountry')
                    ->once()
                    ->with(999999999)
                    ->andReturn([]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/countries/999999999/states');

        $response
            ->assertOk()
            ->assertJson([
                'data' => [],
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function test_verified_user_receives_empty_cities_data_contract(): void
    {
        $this->mock(
            GeographicQueryInterface::class,
            function ($mock): void {
                $mock->shouldReceive('citiesByState')
                    ->once()
                    ->with(999999999)
                    ->andReturn([]);
            },
        );

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/states/999999999/cities');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);
    }
}
