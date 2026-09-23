<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
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
}
