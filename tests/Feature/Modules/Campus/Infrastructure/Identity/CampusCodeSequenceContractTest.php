<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Infrastructure\Identity\CampusCodeSequence;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Mockery;
use PDOException;
use RuntimeException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CampusCodeSequenceContractTest extends TestCase
{
    #[Test]
    public function duplicate_key_conflict_without_a_visible_row_fails_explicitly(): void
    {
        $institutionId = '01JTESTINSTITUTION000000021';

        $existsQuery = Mockery::mock(Builder::class);
        $existsQuery->shouldReceive('where')
            ->once()
            ->with('institution_id', $institutionId)
            ->andReturnSelf();
        $existsQuery->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $duplicateException = $this->queryException(
            1062,
            'Duplicate entry during sequence initialization.',
        );

        $insertQuery = Mockery::mock(Builder::class);
        $insertQuery->shouldReceive('insert')
            ->once()
            ->andThrow($duplicateException);

        $readQuery = Mockery::mock(Builder::class);
        $readQuery->shouldReceive('where')
            ->once()
            ->with('institution_id', $institutionId)
            ->andReturnSelf();
        $readQuery->shouldReceive('lockForUpdate')
            ->once()
            ->andReturnSelf();
        $readQuery->shouldReceive('first')
            ->once()
            ->with(['current_value'])
            ->andReturnNull();

        $this->mockTransaction();

        DB::shouldReceive('table')
            ->with('campus_code_sequences')
            ->times(3)
            ->andReturn($existsQuery, $insertQuery, $readQuery);

        try {
            (new CampusCodeSequence())->next($institutionId);
            $this->fail('La secuencia debe fallar si la fila no existe tras el conflicto.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Campus code sequence could not be initialized.',
                $exception->getMessage(),
            );
        }
    }

    #[Test]
    public function non_duplicate_initialization_error_is_propagated(): void
    {
        $institutionId = '01JTESTINSTITUTION000000022';

        $existsQuery = Mockery::mock(Builder::class);
        $existsQuery->shouldReceive('where')
            ->once()
            ->with('institution_id', $institutionId)
            ->andReturnSelf();
        $existsQuery->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $databaseException = $this->queryException(
            2002,
            'Simulated connection failure during sequence initialization.',
        );

        $insertQuery = Mockery::mock(Builder::class);
        $insertQuery->shouldReceive('insert')
            ->once()
            ->andThrow($databaseException);

        $this->mockTransaction();

        DB::shouldReceive('table')
            ->with('campus_code_sequences')
            ->twice()
            ->andReturn($existsQuery, $insertQuery);

        try {
            (new CampusCodeSequence())->next($institutionId);
            $this->fail('El error ajeno a unicidad debe propagarse.');
        } catch (QueryException $exception) {
            $this->assertSame($databaseException, $exception);
        }
    }

    private function mockTransaction(): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->with(Mockery::type(Closure::class))
            ->andReturnUsing(
                static fn (Closure $callback): mixed => $callback(),
            );
    }

    private function queryException(
        int $driverCode,
        string $message,
    ): QueryException {
        $previous = new PDOException($message);

        $exception = new QueryException(
            'mysql',
            'insert into `campus_code_sequences` (`institution_id`) values (?)',
            [],
            $previous,
        );

        $exception->errorInfo = [
            $driverCode === 1062 ? '23000' : 'HY000',
            $driverCode,
            $message,
        ];

        return $exception;
    }
}
