<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Diagnostics;

use App\Core\Module\Diagnostics\StageRecord;
use App\Core\Module\Diagnostics\StageStatus;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class StageRecordTest extends TestCase
{
    public function test_creates_running_record(): void
    {
        $startedAt = microtime(true);

        $record = new StageRecord(
            stage: 'Manifest Discovery',
            status: StageStatus::Running,
            startedAt: $startedAt,
        );

        $this->assertSame('Manifest Discovery', $record->stage);
        $this->assertSame(StageStatus::Running, $record->status);
        $this->assertSame($startedAt, $record->startedAt);
        $this->assertNull($record->finishedAt);
        $this->assertNull($record->exception);
    }

    public function test_duration_is_null_while_running(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 100.0,
        );

        $this->assertNull($record->duration());
    }

    public function test_with_success_creates_new_instance(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 100.0,
        );

        $success = $record->withSuccess(150.0);

        $this->assertNotSame($record, $success);

        $this->assertSame(StageStatus::Success, $success->status);
        $this->assertSame(100.0, $success->startedAt);
        $this->assertSame(150.0, $success->finishedAt);
        $this->assertNull($success->exception);
    }

    public function test_with_failure_creates_new_instance(): void
    {
        $exception = new RuntimeException('Failure');

        $record = new StageRecord(
            stage: 'Provider Registration',
            status: StageStatus::Running,
            startedAt: 25.0,
        );

        $failed = $record->withFailure(
            exception: $exception,
            finishedAt: 40.0,
        );

        $this->assertNotSame($record, $failed);

        $this->assertSame(StageStatus::Failed, $failed->status);
        $this->assertSame($exception, $failed->exception);
        $this->assertSame(25.0, $failed->startedAt);
        $this->assertSame(40.0, $failed->finishedAt);
    }

    public function test_with_skipped_creates_new_instance(): void
    {
        $record = new StageRecord(
            stage: 'Boot Complete',
            status: StageStatus::Running,
            startedAt: 12.0,
        );

        $skipped = $record->withSkipped(20.0);

        $this->assertSame(StageStatus::Skipped, $skipped->status);
        $this->assertSame(12.0, $skipped->startedAt);
        $this->assertSame(20.0, $skipped->finishedAt);
        $this->assertNull($skipped->exception);
    }

    public function test_duration_is_computed_after_success(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 10.0,
        );

        $record = $record->withSuccess(15.5);

        $this->assertEquals(5.5, $record->duration());
    }

    public function test_duration_is_computed_after_failure(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 8.0,
        );

        $record = $record->withFailure(
            new RuntimeException(),
            10.5,
        );

        $this->assertEquals(2.5, $record->duration());
    }

    public function test_started_at_is_preserved(): void
    {
        $record = new StageRecord(
            stage: 'Manifest',
            status: StageStatus::Running,
            startedAt: 123.456,
        );

        $success = $record->withSuccess(130.000);

        $this->assertSame(
            $record->startedAt,
            $success->startedAt,
        );
    }

    public function test_exception_is_preserved(): void
    {
        $exception = new RuntimeException('Boom');

        $record = new StageRecord(
            stage: 'Provider',
            status: StageStatus::Running,
            startedAt: 1.0,
        );

        $failed = $record->withFailure(
            $exception,
            2.0,
        );

        $this->assertSame(
            $exception,
            $failed->exception,
        );
    }

    public function test_success_does_not_modify_original(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 5.0,
        );

        $record->withSuccess(10.0);

        $this->assertSame(StageStatus::Running, $record->status);
        $this->assertNull($record->finishedAt);
    }

    public function test_failure_does_not_modify_original(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 5.0,
        );

        $record->withFailure(
            new RuntimeException(),
            10.0,
        );

        $this->assertSame(StageStatus::Running, $record->status);
        $this->assertNull($record->finishedAt);
    }

    public function test_skipped_does_not_modify_original(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 5.0,
        );

        $record->withSkipped(10.0);

        $this->assertSame(StageStatus::Running, $record->status);
        $this->assertNull($record->finishedAt);
    }

    public function test_finished_at_is_greater_than_started_at(): void
    {
        $record = new StageRecord(
            stage: 'Lifecycle',
            status: StageStatus::Running,
            startedAt: 20.0,
        );

        $record = $record->withSuccess(25.0);

        $this->assertGreaterThan(
            $record->startedAt,
            $record->finishedAt
        );
    }
}
