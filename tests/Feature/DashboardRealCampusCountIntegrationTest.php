<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Models\User;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

final class DashboardRealCampusCountIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'cenicom',
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');

        DB::connection('mysql')->beginTransaction();
    }

    protected function tearDown(): void
    {
        try {
            $connection = DB::connection('mysql');

            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_dashboard_displays_real_campus_count_from_mysql(): void
    {
        $reader = Mockery::mock(RecentActivityReaderInterface::class);

        $reader
            ->shouldReceive('recent')
            ->once()
            ->with(5)
            ->andReturn([]);

        $this->app->instance(
            RecentActivityReaderInterface::class,
            $reader,
        );

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $expectedCount = DB::connection('mysql')
            ->table('campuses')
            ->count();

        $campusService = app(CampusServiceInterface::class);

        $this->assertSame(
            $expectedCount,
            $campusService->count(),
        );

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('totalCampuses', $expectedCount)
            ->assertSee('Total de sedes registradas')
            ->assertSee((string) $expectedCount);
    }
}
