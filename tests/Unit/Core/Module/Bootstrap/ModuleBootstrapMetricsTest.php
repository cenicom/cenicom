<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleBootstrapMetrics;
use Tests\TestCase;

final class ModuleBootstrapMetricsTest extends TestCase
{
    public function test_creates_empty_metrics(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        self::assertNull(
            $metrics->startedAt()
        );

        self::assertNull(
            $metrics->completedAt()
        );

        self::assertNull(
            $metrics->duration()
        );

        self::assertSame(
            0,
            $metrics->registered()
        );

        self::assertSame(
            0,
            $metrics->skipped()
        );

        self::assertSame(
            0,
            $metrics->failed()
        );
    }


    public function test_captures_start_time(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->start();


        self::assertNotNull(
            $metrics->startedAt()
        );
    }


    public function test_captures_completion_time(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->complete();


        self::assertNotNull(
            $metrics->completedAt()
        );
    }


    public function test_duration_is_null_before_completion(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->start();


        self::assertNull(
            $metrics->duration()
        );
    }


    public function test_calculates_duration(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->start();

        usleep(1000);

        $metrics->complete();


        self::assertNotNull(
            $metrics->duration()
        );

        self::assertGreaterThan(
            0,
            $metrics->duration()
        );
    }


    public function test_increments_registered_modules(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->incrementRegistered();

        $metrics->incrementRegistered();


        self::assertSame(
            2,
            $metrics->registered()
        );
    }


    public function test_increments_skipped_modules(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->incrementSkipped();


        self::assertSame(
            1,
            $metrics->skipped()
        );
    }


    public function test_increments_failed_modules(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->incrementFailed();


        self::assertSame(
            1,
            $metrics->failed()
        );
    }


    public function test_stores_combined_metrics(): void
    {
        $metrics = new ModuleBootstrapMetrics();


        $metrics->start();

        $metrics->incrementRegistered();
        $metrics->incrementRegistered();

        $metrics->incrementSkipped();

        $metrics->incrementFailed();

        $metrics->complete();


        self::assertSame(
            2,
            $metrics->registered()
        );

        self::assertSame(
            1,
            $metrics->skipped()
        );

        self::assertSame(
            1,
            $metrics->failed()
        );

        self::assertNotNull(
            $metrics->duration()
        );
    }
}
