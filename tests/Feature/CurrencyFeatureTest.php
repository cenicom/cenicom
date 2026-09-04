<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Pruebas funcionales del módulo Currency.
 *
 * @package Tests\Feature
 */
final class CurrencyFeatureTest
    extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_can_be_displayed(): void
    {
        $response = $this->get(
            route('currencies.index')
        );

        $response->assertOk();
    }

    public function test_create_page_can_be_displayed(): void
    {
        $response = $this->get(
            route('currencies.create')
        );

        $response->assertOk();
    }
}
