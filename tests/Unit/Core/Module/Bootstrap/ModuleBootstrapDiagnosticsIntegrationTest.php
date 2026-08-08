<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\ModuleBootstrapDiagnostics;
use Tests\TestCase;

final class ModuleBootstrapDiagnosticsIntegrationTest extends TestCase
{
    public function test_context_exposes_diagnostics(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Inventory/module.php'
        );


        self::assertInstanceOf(
            ModuleBootstrapDiagnostics::class,
            $context->diagnostics()
        );
    }


    public function test_diagnostics_receives_manifest_path(): void
    {
        $manifest = '/modules/Inventory/module.php';


        $context = new ModuleBootstrapContext(
            $manifest
        );


        self::assertSame(
            $manifest,
            $context
                ->diagnostics()
                ->manifestPath()
        );
    }


    public function test_setting_context_exception_updates_diagnostics(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Broken/module.php'
        );


        $exception = new \RuntimeException(
            'Definition creation failed'
        );


        $context->setException(
            $exception
        );


        self::assertSame(
            $exception,
            $context
                ->diagnostics()
                ->exception()
        );
    }


    public function test_diagnostics_reports_failure_state(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Broken/module.php'
        );


        self::assertFalse(
            $context
                ->diagnostics()
                ->hasFailure()
        );


        $context->setException(
            new \LogicException(
                'Provider failure'
            )
        );


        self::assertTrue(
            $context
                ->diagnostics()
                ->hasFailure()
        );
    }


    public function test_diagnostics_survives_context_lifecycle(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Test/module.php'
        );


        $diagnostics = $context->diagnostics();


        $exception = new \RuntimeException(
            'Bootstrap error'
        );

        $context->setException(
            $exception
        );


        self::assertSame(
            $diagnostics,
            $context->diagnostics()
        );


        self::assertTrue(
            $context
                ->diagnostics()
                ->hasFailure()
        );

        self::assertSame(
            $exception,
            $diagnostics->exception()
        );

        self::assertSame(
            'Bootstrap error',
            $diagnostics->exception()->getMessage()
        );
    }

    public function test_clearing_context_exception_updates_diagnostics(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Test/module.php'
        );

        $context->setException(
            new \RuntimeException('Failure')
        );

        $context->clearException();

        self::assertFalse(
            $context
                ->diagnostics()
                ->hasFailure()
        );

        self::assertNull(
            $context
                ->diagnostics()
                ->exception()
        );
    }
}
