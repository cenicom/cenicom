<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Services;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Services\InstitutionService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

final class InstitutionServiceTest extends TestCase
{
    private const INSTITUTION_ID = '01ABC';

    public function test_delete_is_blocked_when_campuses_exist(): void
    {
        $this->expectDependency('campuses', true);

        $repository = Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $repository->shouldNotReceive('delete');

        $service = new InstitutionService($repository);

        self::assertFalse(
            $service->delete(self::INSTITUTION_ID),
        );
    }

    public function test_delete_is_blocked_when_institution_addresses_exist(): void
    {
        $this->expectDependency('campuses', false);
        $this->expectDependency('institution_addresses', true);

        $repository = Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $repository->shouldNotReceive('delete');

        $service = new InstitutionService($repository);

        self::assertFalse(
            $service->delete(self::INSTITUTION_ID),
        );
    }

    public function test_delete_is_blocked_when_campus_code_sequences_exist(): void
    {
        $this->expectDependency('campuses', false);
        $this->expectDependency('institution_addresses', false);
        $this->expectDependency('campus_code_sequences', true);

        $repository = Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $repository->shouldNotReceive('delete');

        $service = new InstitutionService($repository);

        self::assertFalse(
            $service->delete(self::INSTITUTION_ID),
        );
    }

    public function test_delete_delegates_to_repository_when_no_dependencies_exist(): void
    {
        $this->expectDependency('campuses', false);
        $this->expectDependency('institution_addresses', false);
        $this->expectDependency('campus_code_sequences', false);

        $repository = Mockery::mock(
            InstitutionRepositoryInterface::class,
        );

        $repository->shouldReceive('delete')
            ->once()
            ->with(self::INSTITUTION_ID)
            ->andReturn(true);

        $service = new InstitutionService($repository);

        self::assertTrue(
            $service->delete(self::INSTITUTION_ID),
        );
    }

    private function expectDependency(
        string $table,
        bool $exists,
    ): void {
        $query = Mockery::mock();

        DB::shouldReceive('table')
            ->once()
            ->with($table)
            ->andReturn($query);

        $query->shouldReceive('where')
            ->once()
            ->with('institution_id', self::INSTITUTION_ID)
            ->andReturnSelf();

        $query->shouldReceive('exists')
            ->once()
            ->andReturn($exists);
    }
}
