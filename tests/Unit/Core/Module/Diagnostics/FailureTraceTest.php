<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Diagnostics;

use App\Core\Module\Diagnostics\FailureTrace;
use App\Core\Module\Diagnostics\StageStatus;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FailureTraceTest extends TestCase
{
    private FailureTrace $trace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trace = new FailureTrace();
    }

    public function test_start_registers_stage(): void
    {
        $this->trace->start('Manifest Discovery');

        $this->assertTrue(
            $this->trace->hasStage('Manifest Discovery')
        );
    }

    public function test_start_registers_running_state(): void
    {
        $this->trace->start('Lifecycle');

        $record = $this->trace->stage('Lifecycle');

        $this->assertNotNull($record);
        $this->assertSame(StageStatus::Running, $record->status);
        $this->assertNull($record->finishedAt);
    }

    public function test_start_twice_throws_exception(): void
    {
        $this->trace->start('Lifecycle');

        $this->expectException(LogicException::class);

        $this->trace->start('Lifecycle');
    }

    public function test_empty_stage_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $this->trace->start('');
    }

    public function test_finish_changes_status_to_success(): void
    {
        $this->trace->start('Providers');

        usleep(1000);

        $this->trace->finish('Providers');

        $record = $this->trace->stage('Providers');

        $this->assertSame(StageStatus::Success, $record->status);
    }

    public function test_finish_sets_finished_at(): void
    {
        $this->trace->start('Providers');

        usleep(1000);

        $this->trace->finish('Providers');

        $this->assertNotNull(
            $this->trace->stage('Providers')->finishedAt
        );
    }

    public function test_finish_computes_duration(): void
    {
        $this->trace->start('Providers');

        usleep(1000);

        $this->trace->finish('Providers');

        $this->assertGreaterThan(
            0,
            $this->trace->stage('Providers')->duration()
        );
    }

    public function test_finish_without_start_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $this->trace->finish('Providers');
    }

    public function test_finish_twice_throws_exception(): void
    {
        $this->trace->start('Providers');
        $this->trace->finish('Providers');

        $this->expectException(LogicException::class);

        $this->trace->finish('Providers');
    }

    public function test_fail_changes_status_to_failed(): void
    {
        $this->trace->start('Registration');

        $this->trace->fail(
            'Registration',
            new RuntimeException('Boom')
        );

        $record = $this->trace->stage('Registration');

        $this->assertSame(StageStatus::Failed, $record->status);
    }

    public function test_fail_stores_exception(): void
    {
        $exception = new RuntimeException('Boom');

        $this->trace->start('Registration');

        $this->trace->fail(
            'Registration',
            $exception
        );

        $this->assertSame(
            $exception,
            $this->trace->stage('Registration')->exception
        );
    }

    public function test_fail_without_start_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $this->trace->fail(
            'Registration',
            new RuntimeException()
        );
    }

    public function test_fail_twice_throws_exception(): void
    {
        $this->trace->start('Registration');

        $this->trace->fail(
            'Registration',
            new RuntimeException()
        );

        $this->expectException(LogicException::class);

        $this->trace->fail(
            'Registration',
            new RuntimeException()
        );
    }

    public function test_skip_changes_status_to_skipped(): void
    {
        $this->trace->start('Optional');

        $this->trace->skip('Optional');

        $this->assertSame(
            StageStatus::Skipped,
            $this->trace->stage('Optional')->status
        );
    }

    public function test_skip_without_start_throws_exception(): void
    {
        $this->expectException(LogicException::class);

        $this->trace->skip('Optional');
    }

    public function test_current_returns_last_stage(): void
    {
        $this->trace->start('One');
        $this->trace->start('Two');
        $this->trace->start('Three');

        $this->assertSame(
            'Three',
            $this->trace->current()->stage
        );
    }

    public function test_current_returns_null_when_empty(): void
    {
        $this->assertNull(
            $this->trace->current()
        );
    }

    public function test_stage_returns_registered_stage(): void
    {
        $this->trace->start('Manifest');

        $this->assertSame(
            'Manifest',
            $this->trace->stage('Manifest')->stage
        );
    }

    public function test_stage_returns_null_for_unknown_stage(): void
    {
        $this->assertNull(
            $this->trace->stage('Unknown')
        );
    }

    public function test_has_stage_returns_true(): void
    {
        $this->trace->start('Manifest');

        $this->assertTrue(
            $this->trace->hasStage('Manifest')
        );
    }

    public function test_has_stage_returns_false(): void
    {
        $this->assertFalse(
            $this->trace->hasStage('Manifest')
        );
    }

        public function test_timeline_returns_all_registered_stages(): void
    {
        $this->trace->start('One');
        $this->trace->start('Two');
        $this->trace->start('Three');

        $timeline = $this->trace->timeline();

        $this->assertCount(3, $timeline);
    }

    public function test_timeline_preserves_insertion_order(): void
    {
        $this->trace->start('Manifest');
        $this->trace->start('Definition');
        $this->trace->start('Lifecycle');

        $timeline = $this->trace->timeline();

        $this->assertSame('Manifest', $timeline[0]->stage);
        $this->assertSame('Definition', $timeline[1]->stage);
        $this->assertSame('Lifecycle', $timeline[2]->stage);
    }

    public function test_failure_point_returns_null_when_successful(): void
    {
        $this->trace->start('Manifest');
        $this->trace->finish('Manifest');

        $this->assertNull(
            $this->trace->failurePoint()
        );
    }

    public function test_failure_point_returns_failed_stage(): void
    {
        $exception = new RuntimeException('Failure');

        $this->trace->start('Registration');

        $this->trace->fail(
            'Registration',
            $exception
        );

        $failure = $this->trace->failurePoint();

        $this->assertNotNull($failure);
        $this->assertSame(
            StageStatus::Failed,
            $failure->status
        );
        $this->assertSame(
            $exception,
            $failure->exception
        );
    }

    public function test_has_failures_returns_false(): void
    {
        $this->trace->start('Manifest');
        $this->trace->finish('Manifest');

        $this->assertFalse(
            $this->trace->hasFailures()
        );
    }

    public function test_has_failures_returns_true(): void
    {
        $this->trace->start('Manifest');

        $this->trace->fail(
            'Manifest',
            new RuntimeException()
        );

        $this->assertTrue(
            $this->trace->hasFailures()
        );
    }

    public function test_count_returns_number_of_registered_stages(): void
    {
        $this->trace->start('One');
        $this->trace->start('Two');
        $this->trace->start('Three');

        $this->assertSame(
            3,
            $this->trace->count()
        );
    }

    public function test_total_duration_returns_zero_when_empty(): void
    {
        $this->assertSame(
            0.0,
            $this->trace->totalDuration()
        );
    }

    public function test_total_duration_ignores_running_stages(): void
    {
        $this->trace->start('Finished');

        usleep(1000);

        $this->trace->finish('Finished');

        $this->trace->start('Running');

        $this->assertGreaterThan(
            0,
            $this->trace->totalDuration()
        );
    }

    public function test_total_duration_is_sum_of_finished_stages(): void
    {
        $this->trace->start('One');

        usleep(1000);

        $this->trace->finish('One');

        $this->trace->start('Two');

        usleep(1000);

        $this->trace->finish('Two');

        $this->assertGreaterThan(
            0,
            $this->trace->totalDuration()
        );
    }

    public function test_clear_removes_all_registered_stages(): void
    {
        $this->trace->start('Manifest');
        $this->trace->start('Lifecycle');

        $this->trace->clear();

        $this->assertSame(
            0,
            $this->trace->count()
        );

        $this->assertNull(
            $this->trace->current()
        );

        $this->assertFalse(
            $this->trace->hasStage('Manifest')
        );
    }

    public function test_clear_removes_failure_state(): void
    {
        $this->trace->start('Registration');

        $this->trace->fail(
            'Registration',
            new RuntimeException()
        );

        $this->trace->clear();

        $this->assertFalse(
            $this->trace->hasFailures()
        );

        $this->assertNull(
            $this->trace->failurePoint()
        );
    }
}
