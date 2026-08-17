<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\NullModuleBootstrapReporter;
use App\Core\Module\DTO\ModuleDefinition;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class NullModuleBootstrapReporterTest extends TestCase
{
    public function test_module_discovered_does_not_throw(): void
    {
        $reporter = new NullModuleBootstrapReporter();

        $reporter->moduleDiscovered('modules/example/module.json');

        $this->addToAssertionCount(1);
    }

    public function test_module_loaded_does_not_throw(): void
    {
        $reporter = new NullModuleBootstrapReporter();

        $reporter->moduleLoaded(
            $this->moduleDefinition()
        );

        $this->addToAssertionCount(1);
    }

    public function test_module_skipped_does_not_throw(): void
    {
        $reporter = new NullModuleBootstrapReporter();

        $reporter->moduleSkipped(
            $this->moduleDefinition(),
            'Module intentionally skipped.'
        );

        $this->addToAssertionCount(1);
    }

    public function test_module_failed_does_not_throw(): void
    {
        $reporter = new NullModuleBootstrapReporter();

        $reporter->moduleFailed(
            'modules/example/module.json',
            new RuntimeException('Bootstrap failure.')
        );

        $this->addToAssertionCount(1);
    }

    private function moduleDefinition(): ModuleDefinition
    {
        return new ModuleDefinition(
            name: 'Example',
            namespace: 'App\\Modules\\Example',
            basePath: 'app/Modules/Example',
            manifestPath: 'app/Modules/Example/module.json',
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            enabled: true,
        );
    }
}
