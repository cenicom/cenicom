<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapPipeline;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use App\Core\Module\Bootstrap\ModuleProviderValidator;
use App\Core\Module\Bootstrap\Stages\CreateDefinitionStage;
use App\Core\Module\Bootstrap\Stages\RegisterModuleStage;
use App\Core\Module\Bootstrap\Stages\RegisterProvidersStage;
use App\Core\Module\Bootstrap\Stages\ValidationStage;
use App\Core\Module\Factory\ModuleDefinitionFactory;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use App\Core\Module\Registry\ModuleRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapEndToEndTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $finder;

    private ModuleRegistry $registry;

    private ModuleBootstrap $bootstrap;

    private ModuleProviderRegistrar $providerRegistrar;

    private ModuleLifecycleManager $lifecycle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->registry = new ModuleRegistry();

        $validator = new ModuleProviderValidator();

        $this->providerRegistrar = new ModuleProviderRegistrar(
            $validator,
            $this->app
        );

        $this->lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $factory = new ModuleDefinitionFactory();

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                $factory,
                $this->lifecycle,
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $this->registry,
                $this->lifecycle,
            ),
            new RegisterProvidersStage(
                $this->providerRegistrar,
            ),
        ]);

        $this->bootstrap = new ModuleBootstrap(
            $this->finder,
            $pipeline,
            $this->lifecycle,
        );
    }

    /**
     * E2E-001
     * Bootstrap registra múltiples módulos válidos.
     */
    public function test_bootstrap_registers_multiple_modules(): void
    {
        $manifests = [
            base_path('tests/Fixtures/Modules/Blog/module.php'),
            base_path('tests/Fixtures/Modules/Users/module.php'),
            base_path('tests/Fixtures/Modules/Inventory/module.php'),
        ];

        $this->finder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $this->bootstrap->bootstrap();

        $this->assertSame(
            3,
            $this->registry->count()
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertTrue(
            $this->registry->has('Users')
        );

        $this->assertTrue(
            $this->registry->has('Inventory')
        );

        $this->assertSame(
            [
                'Blog',
                'Users',
                'Inventory',
            ],
            $this->registry->names()
        );
    }

    /**
     * E2E-002
     * Objetivo
     *   Certificar que:
     *       Blog → enabled = true → se registra.
     *       Users → enabled = false → se omite (skipped).
     *       Inventory → enabled = true → se registra.
     *       Sin que el bootstrap falle.
     */
    public function test_bootstrap_skips_disabled_modules(): void
    {
        $manifests = [
            base_path('tests/Fixtures/Modules/Blog/module.php'),
            base_path('tests/Fixtures/Modules/UsersDisabled/module.php'),
            base_path('tests/Fixtures/Modules/Inventory/module.php'),
        ];

        $this->finder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $this->bootstrap->bootstrap();

        $this->assertSame(
            2,
            $this->registry->count()
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertFalse(
            $this->registry->has('Users')
        );

        $this->assertTrue(
            $this->registry->has('Inventory')
        );

        $this->assertSame(
            [
                'Blog',
                'Inventory',
            ],
            $this->registry->names()
        );
    }

    /* E2E-003
     * Bootstrap continúa cuando un módulo falla (Fail Safe) */
    public function test_bootstrap_continues_when_module_definition_fails(): void
    {
        $manifests = [
            base_path('tests/Fixtures/Modules/Blog/module.php'),
            base_path('tests/Fixtures/Modules/Broken/module.php'),
            base_path('tests/Fixtures/Modules/Inventory/module.php'),
        ];

        $this->finder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $this->bootstrap->bootstrap();

        $this->assertSame(
            2,
            $this->registry->count()
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertFalse(
            $this->registry->has('Broken')
        );

        $this->assertTrue(
            $this->registry->has('Inventory')
        );

        $this->assertSame(
            [
                'Blog',
                'Inventory',
            ],
            $this->registry->names()
        );
    }

    /* 🚢 ERP-INT-004.6.4 — E2E-004 */
    public function test_bootstrap_is_idempotent(): void
    {
        $manifests = [
            base_path('tests/Fixtures/Modules/Blog/module.php'),
            base_path('tests/Fixtures/Modules/Inventory/module.php'),
        ];

        $this->finder
            ->expects($this->exactly(2))
            ->method('find')
            ->willReturn($manifests);

        $this->bootstrap->bootstrap();

        $this->assertSame(
            2,
            $this->registry->count()
        );

        $this->bootstrap->bootstrap();

        $this->assertSame(
            2,
            $this->registry->count()
        );

        $this->assertSame(
            [
                'Blog',
                'Inventory',
            ],
            $this->registry->names()
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertTrue(
            $this->registry->has('Inventory')
        );
    }

    /* E2E-006
     * Stress Test del Bootstrap
    * Esta prueba no busca medir rendimiento en milisegundos.
    * Su objetivo es certificar que el Bootstrap mantiene sus invariantes
    * cuando procesa un gran número de módulos. */
    public function test_bootstrap_handles_many_modules(): void
    {
        $manifests = [];

        for ($i = 1; $i <= 100; $i++) {

            $module = sprintf('Module%03d', $i);

            $manifest = tempnam(
                sys_get_temp_dir(),
                'module_'
            );

            file_put_contents(
                $manifest,
                '<?php return ' . var_export([
                    'name' => $module,
                    'namespace' => "Tests\\Fixtures\\Modules\\{$module}",
                    'providers' => [],
                    'enabled' => true,
                ], true) . ';'
            );

            $manifests[] = $manifest;
        }

        $this->finder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        try {
            $this->bootstrap->bootstrap();

            self::assertSame(
                100,
                $this->registry->count()
            );

            foreach (range(1, 100) as $i) {
                self::assertTrue(
                    $this->registry->has(
                        sprintf('Module%03d', $i)
                    )
                );
            }
        } finally {
            foreach ($manifests as $manifest) {
                @unlink($manifest);
            }
        }
    }
}
