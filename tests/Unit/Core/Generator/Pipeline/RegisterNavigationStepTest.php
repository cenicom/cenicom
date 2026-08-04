<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\RegisterNavigationStep;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use Mockery;
use Tests\TestCase;

final class RegisterNavigationStepTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_step_implements_pipeline_contract(): void
    {
        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $step = new RegisterNavigationStep($registrar);

        self::assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_registers_navigation_groups(): void
    {
        $module = $this->moduleWithNavigation();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar
            ->shouldReceive('group')
            ->once()
            ->with($module->navigation()->groups[0])
            ->andReturnSelf();

        $registrar
            ->shouldReceive('item')
            ->once()
            ->with($module->navigation()->items[0])
            ->andReturnSelf();

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            new GeneratorResult(),
            fn (
                ModuleData $module,
                GeneratorResult $result
            ) => $result,
        );

        $this->addToAssertionCount(1);
    }

    public function test_registers_navigation_items(): void
    {
        $module = $this->moduleWithNavigation();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar
            ->shouldReceive('group')
            ->once()
            ->andReturnSelf();

        $registrar
            ->shouldReceive('item')
            ->once()
            ->andReturnSelf();

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            new GeneratorResult(),
            fn (
                ModuleData $module,
                GeneratorResult $result
            ) => $result,
        );

        $this->addToAssertionCount(1);
    }

    public function test_skips_registration_when_manifest_is_empty(): void
    {
        $module = $this->moduleWithoutNavigation();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar->shouldNotReceive('group');
        $registrar->shouldNotReceive('item');

        $called = false;

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            new GeneratorResult(),
            function () use (&$called): GeneratorResult {

                $called = true;

                return new GeneratorResult();
            }
        );

        self::assertTrue($called);
    }

    public function test_pipeline_continues_after_registration(): void
    {
        $module = $this->moduleWithNavigation();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar->shouldReceive('group')->once()->andReturnSelf();
        $registrar->shouldReceive('item')->once()->andReturnSelf();

        $called = false;

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            new GeneratorResult(),
            function () use (&$called): GeneratorResult {

                $called = true;

                return new GeneratorResult();
            }
        );

        self::assertTrue($called);
    }

    public function test_preserves_same_module_instance(): void
    {
        $module = $this->moduleWithNavigation();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar->shouldReceive('group')->once()->andReturnSelf();
        $registrar->shouldReceive('item')->once()->andReturnSelf();

        $received = null;

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            new GeneratorResult(),
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$received): GeneratorResult {

                $received = $module;

                return $result;
            }
        );

        self::assertSame($module, $received);
    }

    public function test_preserves_same_generator_result(): void
    {
        $module = $this->moduleWithNavigation();

        $result = new GeneratorResult();

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar->shouldReceive('group')->once()->andReturnSelf();
        $registrar->shouldReceive('item')->once()->andReturnSelf();

        $received = null;

        $step = new RegisterNavigationStep($registrar);

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$received): GeneratorResult {

                $received = $result;

                return $result;
            }
        );

        self::assertSame($result, $received);
    }

    private function moduleWithNavigation(): ModuleData
    {
        return (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],
            'navigation' => [
                'groups' => [
                    [
                        'id' => 'catalogs',
                        'label' => 'Catalogs',
                        'icon' => 'bi-folder',
                        'order' => 10,
                    ],
                ],
                'items' => [
                    [
                        'id' => 'currencies',
                        'label' => 'Currencies',
                        'route' => 'currencies.index',
                        'group' => 'catalogs',
                        'icon' => 'bi-cash',
                        'order' => 10,
                    ],
                ],
            ],
            'fields' => [],
        ]);
    }

    private function moduleWithoutNavigation(): ModuleData
    {
        return (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],
            'navigation' => [],
            'fields' => [],
        ]);
    }
}
