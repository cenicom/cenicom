<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Diagnostics;

use App\Core\Module\Diagnostics\StageRecord;
use App\Core\Module\Diagnostics\StageStatus;
use App\Core\Module\Diagnostics\StageTimeline;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class StageTimelineTest extends TestCase
{
    public function test_all_returns_all_records(): void
    {
        $timeline = new StageTimeline([
            $this->runningRecord('Manifest', 1.0),
            $this->runningRecord('Lifecycle', 2.0),
        ]);

        $records = $timeline->all();

        $this->assertCount(2, $records);
        $this->assertSame('Manifest', $records[0]->stage);
        $this->assertSame('Lifecycle', $records[1]->stage);
    }

    public function test_first_returns_first_record(): void
    {
        $first = $this->runningRecord('Manifest', 1.0);

        $timeline = new StageTimeline([
            $first,
            $this->runningRecord('Lifecycle', 2.0),
        ]);

        $this->assertSame($first, $timeline->first());
    }

    public function test_first_returns_null_when_empty(): void
    {
        $timeline = new StageTimeline([]);

        $this->assertNull($timeline->first());
    }

    public function test_last_returns_last_record(): void
    {
        $last = $this->runningRecord('Boot Complete', 3.0);

        $timeline = new StageTimeline([
            $this->runningRecord('Manifest', 1.0),
            $last,
        ]);

        $this->assertSame($last, $timeline->last());
    }

    public function test_last_returns_null_when_empty(): void
    {
        $timeline = new StageTimeline([]);

        $this->assertNull($timeline->last());
    }

    public function test_failure_point_returns_null_when_no_failures(): void
    {
        $timeline = new StageTimeline([
            $this->successRecord('Manifest'),
            $this->successRecord('Lifecycle'),
        ]);

        $this->assertNull($timeline->failurePoint());
    }

    public function test_failure_point_returns_failed_record(): void
    {
        $failed = $this->failedRecord('Provider Registration');

        $timeline = new StageTimeline([
            $this->successRecord('Manifest'),
            $failed,
            $this->successRecord('Boot Complete'),
        ]);

        $this->assertSame($failed, $timeline->failurePoint());
    }

    public function test_has_failures_returns_false(): void
    {
        $timeline = new StageTimeline([
            $this->successRecord('Manifest'),
        ]);

        $this->assertFalse($timeline->hasFailures());
    }

    public function test_has_failures_returns_true(): void
    {
        $timeline = new StageTimeline([
            $this->failedRecord('Lifecycle'),
        ]);

        $this->assertTrue($timeline->hasFailures());
    }

    public function test_total_duration_returns_sum(): void
    {
        $timeline = new StageTimeline([
            $this->successRecord('Manifest', 10.0, 15.0),     // 5
            $this->successRecord('Lifecycle', 20.0, 23.5),    // 3.5
            $this->successRecord('Provider', 30.0, 31.0),     // 1
        ]);

        $this->assertEquals(9.5, $timeline->totalDuration());
    }

    public function test_total_duration_ignores_running_records(): void
    {
        $timeline = new StageTimeline([
            $this->successRecord('Manifest', 10.0, 15.0),
            $this->runningRecord('Lifecycle', 20.0),
        ]);

        $this->assertEquals(5.0, $timeline->totalDuration());
    }

    public function test_count_returns_total_records(): void
    {
        $timeline = new StageTimeline([
            $this->runningRecord('A', 1.0),
            $this->runningRecord('B', 2.0),
            $this->runningRecord('C', 3.0),
        ]);

        $this->assertCount(3, $timeline);
        $this->assertSame(3, $timeline->count());
    }

    public function test_iterator_preserves_order(): void
    {
        $timeline = new StageTimeline([
            $this->runningRecord('Manifest', 1.0),
            $this->runningRecord('Definition', 2.0),
            $this->runningRecord('Lifecycle', 3.0),
        ]);

        $stages = [];

        foreach ($timeline as $record) {
            $stages[] = $record->stage;
        }

        $this->assertSame([
            'Manifest',
            'Definition',
            'Lifecycle',
        ], $stages);
    }

    public function test_all_preserves_insertion_order(): void
    {
        $timeline = new StageTimeline([
            $this->runningRecord('One', 1),
            $this->runningRecord('Two', 2),
            $this->runningRecord('Three', 3),
        ]);

        $this->assertSame(
            ['One', 'Two', 'Three'],
            array_map(
                static fn (StageRecord $record) => $record->stage,
                $timeline->all(),
            ),
        );
    }

    private function runningRecord(
        string $stage,
        float $startedAt,
    ): StageRecord {
        return new StageRecord(
            stage: $stage,
            status: StageStatus::Running,
            startedAt: $startedAt,
        );
    }

    private function successRecord(
        string $stage,
        float $startedAt = 1.0,
        float $finishedAt = 2.0,
    ): StageRecord {
        return new StageRecord(
            stage: $stage,
            status: StageStatus::Success,
            startedAt: $startedAt,
            finishedAt: $finishedAt,
        );
    }

    private function failedRecord(string $stage): StageRecord
    {
        return new StageRecord(
            stage: $stage,
            status: StageStatus::Failed,
            startedAt: 1.0,
            finishedAt: 2.0,
            exception: new RuntimeException('Failure'),
        );
    }
}
