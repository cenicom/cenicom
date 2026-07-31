<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleBootstrapReport;
use RuntimeException;
use Tests\TestCase;

final class ModuleBootstrapReportTest extends TestCase
{
    public function test_creates_empty_report(): void
    {
        $report = new ModuleBootstrapReport();

        self::assertSame([], $report->registered());
        self::assertSame([], $report->skipped());
        self::assertSame([], $report->failed());
    }


    public function test_stores_registered_module(): void
    {
        $report = new ModuleBootstrapReport();

        $report->addRegistered(
            'Inventory',
            [
                'InventoryServiceProvider',
            ]
        );

        self::assertCount(
            1,
            $report->registered()
        );

        self::assertSame(
            'Inventory',
            $report->registered()[0]['module']
        );
    }


    public function test_stores_skipped_module(): void
    {
        $report = new ModuleBootstrapReport();

        $report->addSkipped(
            'Blog',
            'disabled'
        );

        self::assertCount(
            1,
            $report->skipped()
        );

        self::assertSame(
            'disabled',
            $report->skipped()[0]['reason']
        );
    }


    public function test_stores_failed_module(): void
    {
        $report = new ModuleBootstrapReport();

        $exception = new RuntimeException(
            'Provider failure'
        );

        $report->addFailed(
            'BrokenModule',
            $exception
        );

        self::assertCount(
            1,
            $report->failed()
        );

        self::assertSame(
            $exception,
            $report->failed()[0]['exception']
        );
    }


    public function test_stores_multiple_results(): void
    {
        $report = new ModuleBootstrapReport();

        $report->addRegistered(
            'Inventory',
            []
        );

        $report->addRegistered(
            'Treasury',
            []
        );

        $report->addSkipped(
            'Legacy',
            'disabled'
        );

        self::assertCount(
            2,
            $report->registered()
        );

        self::assertCount(
            1,
            $report->skipped()
        );
    }
}
