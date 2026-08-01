<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Diagnostics;

use App\Core\Module\Diagnostics\StageStatus;
use PHPUnit\Framework\TestCase;

final class StageStatusTest extends TestCase
{
    public function test_enum_contains_expected_cases(): void
    {
        $cases = StageStatus::cases();

        $this->assertCount(5, $cases);

        $this->assertSame(StageStatus::Pending, $cases[0]);
        $this->assertSame(StageStatus::Running, $cases[1]);
        $this->assertSame(StageStatus::Success, $cases[2]);
        $this->assertSame(StageStatus::Failed, $cases[3]);
        $this->assertSame(StageStatus::Skipped, $cases[4]);
    }

    public function test_enum_values_are_correct(): void
    {
        $this->assertSame('pending', StageStatus::Pending->value);
        $this->assertSame('running', StageStatus::Running->value);
        $this->assertSame('success', StageStatus::Success->value);
        $this->assertSame('failed', StageStatus::Failed->value);
        $this->assertSame('skipped', StageStatus::Skipped->value);
    }

    public function test_success_is_final(): void
    {
        $this->assertTrue(StageStatus::Success->isFinal());
    }

    public function test_failed_is_final(): void
    {
        $this->assertTrue(StageStatus::Failed->isFinal());
    }

    public function test_skipped_is_final(): void
    {
        $this->assertTrue(StageStatus::Skipped->isFinal());
    }

    public function test_pending_is_not_final(): void
    {
        $this->assertFalse(StageStatus::Pending->isFinal());
    }

    public function test_running_is_not_final(): void
    {
        $this->assertFalse(StageStatus::Running->isFinal());
    }

    public function test_failed_is_failure(): void
    {
        $this->assertTrue(StageStatus::Failed->isFailure());
    }

    public function test_success_is_not_failure(): void
    {
        $this->assertFalse(StageStatus::Success->isFailure());
    }

    public function test_pending_is_not_failure(): void
    {
        $this->assertFalse(StageStatus::Pending->isFailure());
    }

    public function test_running_is_not_failure(): void
    {
        $this->assertFalse(StageStatus::Running->isFailure());
    }

    public function test_skipped_is_not_failure(): void
    {
        $this->assertFalse(StageStatus::Skipped->isFailure());
    }

    public function test_running_is_running(): void
    {
        $this->assertTrue(StageStatus::Running->isRunning());
    }

    public function test_pending_is_not_running(): void
    {
        $this->assertFalse(StageStatus::Pending->isRunning());
    }

    public function test_success_is_not_running(): void
    {
        $this->assertFalse(StageStatus::Success->isRunning());
    }

    public function test_failed_is_not_running(): void
    {
        $this->assertFalse(StageStatus::Failed->isRunning());
    }

    public function test_skipped_is_not_running(): void
    {
        $this->assertFalse(StageStatus::Skipped->isRunning());
    }

    public function test_enum_case_names_are_stable(): void
    {
        $this->assertSame('Pending', StageStatus::Pending->name);
        $this->assertSame('Running', StageStatus::Running->name);
        $this->assertSame('Success', StageStatus::Success->name);
        $this->assertSame('Failed', StageStatus::Failed->name);
        $this->assertSame('Skipped', StageStatus::Skipped->name);
    }

    public function test_cases_are_unique(): void
    {
        $values = array_map(
            static fn (StageStatus $status): string => $status->value,
            StageStatus::cases()
        );

        $this->assertCount(
            count(array_unique($values)),
            $values
        );
    }

    public function test_failed_is_final_and_failure(): void
    {
        $this->assertTrue(StageStatus::Failed->isFinal());
        $this->assertTrue(StageStatus::Failed->isFailure());
    }

    public function test_running_is_only_running_state(): void
    {
        foreach (StageStatus::cases() as $status) {
            if ($status === StageStatus::Running) {
                $this->assertTrue($status->isRunning());
            } else {
                $this->assertFalse($status->isRunning());
            }
        }
    }
}
