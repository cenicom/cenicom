<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleBootstrapDiagnostics;
use Tests\TestCase;

final class ModuleBootstrapDiagnosticsTest extends TestCase
{
    public function test_creates_empty_diagnostics(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        self::assertNull(
            $diagnostics->manifestPath()
        );

        self::assertNull(
            $diagnostics->moduleName()
        );

        self::assertNull(
            $diagnostics->failedStage()
        );

        self::assertNull(
            $diagnostics->exception()
        );

        self::assertFalse(
            $diagnostics->hasFailure()
        );
    }


    public function test_stores_manifest_path(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        $diagnostics->setManifestPath(
            '/modules/Inventory/module.php'
        );


        self::assertSame(
            '/modules/Inventory/module.php',
            $diagnostics->manifestPath()
        );
    }


    public function test_stores_module_name(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        $diagnostics->setModuleName(
            'Inventory'
        );


        self::assertSame(
            'Inventory',
            $diagnostics->moduleName()
        );
    }


    public function test_stores_failed_stage(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        $diagnostics->setFailedStage(
            'CreateDefinitionStage'
        );


        self::assertSame(
            'CreateDefinitionStage',
            $diagnostics->failedStage()
        );
    }


    public function test_stores_exception(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();

        $exception = new \RuntimeException(
            'Definition creation failed'
        );


        $diagnostics->setException(
            $exception
        );


        self::assertSame(
            $exception,
            $diagnostics->exception()
        );
    }


    public function test_reports_failure_state(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        self::assertFalse(
            $diagnostics->hasFailure()
        );


        $diagnostics->setException(
            new \RuntimeException(
                'Bootstrap failed'
            )
        );


        self::assertTrue(
            $diagnostics->hasFailure()
        );
    }


    public function test_keeps_complete_diagnostic_information(): void
    {
        $diagnostics = new ModuleBootstrapDiagnostics();


        $exception = new \LogicException(
            'Provider registration failed'
        );


        $diagnostics->setManifestPath(
            '/modules/Broken/module.php'
        );

        $diagnostics->setModuleName(
            'Broken'
        );

        $diagnostics->setFailedStage(
            'RegisterProvidersStage'
        );

        $diagnostics->setException(
            $exception
        );


        self::assertSame(
            '/modules/Broken/module.php',
            $diagnostics->manifestPath()
        );

        self::assertSame(
            'Broken',
            $diagnostics->moduleName()
        );

        self::assertSame(
            'RegisterProvidersStage',
            $diagnostics->failedStage()
        );

        self::assertSame(
            $exception,
            $diagnostics->exception()
        );

        self::assertTrue(
            $diagnostics->hasFailure()
        );
    }
}
