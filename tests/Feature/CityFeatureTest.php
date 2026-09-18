<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

use Tests\TestCase;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Pruebas funcionales del módulo City.
 *
 * @package Tests\Feature
 */
final class CityFeatureTest
    extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_can_be_displayed(): void
    {
            $user = User::factory()->create();

    $this->actingAs($user);

        $response = $this->get(
            route('cities.index')
        );

        $response->assertOk();
    }

    public function test_create_page_can_be_displayed(): void
    {
            $user = User::factory()->create();

    $this->actingAs($user);

        $response = $this->get(
            route('cities.create')
        );

        $response->assertOk();
    }
}
