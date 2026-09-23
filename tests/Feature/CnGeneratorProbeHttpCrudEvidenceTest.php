<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CnGeneratorProbeHttpCrudEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_module_supports_http_crud_flow(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $storeResponse = $this->post(
            route('cn_generator_probes.store'),
            []
        );

        $storeResponse->assertRedirect(
            route('cn_generator_probes.index')
        );

        $probe = CnGeneratorProbe::query()
            ->latest('created_at')
            ->first();

        $this->assertNotNull($probe);

        $this->get(
            route('cn_generator_probes.show', $probe)
        )->assertOk();

        $this->get(
            route('cn_generator_probes.edit', $probe)
        )->assertOk();

        $this->put(
            route('cn_generator_probes.update', $probe),
            []
        )->assertRedirect(
            route('cn_generator_probes.index')
        );

        $this->delete(
            route('cn_generator_probes.destroy', $probe)
        )->assertRedirect(
            route('cn_generator_probes.index')
        );

        $this->assertDatabaseMissing(
            'cn_generator_probes',
            [
                'id' => $probe->getKey(),
            ]
        );
    }
}
