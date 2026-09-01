<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;


use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\Steps\ValidateModuleStep;
use App\Core\Generator\Results\GeneratorResult;
//use ReflectionClass;
use Tests\TestCase;

final class ValidateModuleStepTest extends TestCase
{
    private function createModule(
        string $name = 'Currency',
        string $plural = 'Currencies'
    ): ModuleData {
        $factory = app(ModuleDataFactory::class);

        return $factory->create([
            'identity' => [
                'name' => $name,
                'singular' => strtolower($name),
                'plural' => $plural,
                'table' => strtolower($plural),
                'description' => 'Test module',
            ],
            'fields' => [],
        ]);
    }

    /*
    public function test_invalid_module_returns_error(): void
    {
        $step = new ValidateModuleStep();

        $factory = new ModuleDataFactory();

        $module = $factory->create([
            'identity' => [
                'name' => '',
            ],
        ]);

        $result = $step->execute($module);

        $this->assertTrue(
            $result->hasErrors()
        );
    }
*/

    public function test_step_implements_pipeline_contract(): void
    {
        $step = new ValidateModuleStep();

        $this->assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_valid_module_calls_next(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $called = false;

        $returned = $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            }
        );

        self::assertTrue($called);

        self::assertSame(
            $result,
            $returned
        );

        self::assertFalse(
            $returned->hasErrors()
        );
    }

    public function test_invalid_module_stops_pipeline(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule('');

        $result = new GeneratorResult();

        $called = false;

        $returned = $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            }
        );

        self::assertFalse($called);

        self::assertTrue(
            $returned->hasErrors()
        );
    }

    public function test_preserves_same_module_instance(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $receivedModule = null;

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedModule): GeneratorResult {

                $receivedModule = $module;

                return $result;
            }
        );

        self::assertSame(
            $module,
            $receivedModule
        );
    }

    public function test_preserves_same_generator_result(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $receivedResult = null;

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedResult): GeneratorResult {

                $receivedResult = $result;

                return $result;
            }
        );

        self::assertSame(
            $result,
            $receivedResult
        );
    }

    private function createInvalidModule(): ModuleData
    {
        $module = $this->createModule();

        return new ModuleData(
            name: '',
            singular: $module->singular(),
            plural: $module->plural(),
            table: $module->table(),
            description: $module->description(),

            modelNamespace: $module->modelNamespace(),
            repositoryNamespace: $module->repositoryNamespace(),
            serviceNamespace: $module->serviceNamespace(),
            controllerNamespace: $module->controllerNamespace(),
            policyNamespace: $module->policyNamespace(),
            requestNamespace: $module->requestNamespace(),
            factoryNamespace: $module->factoryNamespace(),
            repositoryContractNamespace: $module->repositoryContractNamespace(),
            serviceContractNamespace: $module->serviceContractNamespace(),
            seederNamespace: $module->seederNamespace(),
            testNamespace: $module->testNamespace(),
            observerNamespace: $module->observerNamespace(),
            permissionNamespace: $module->permissionNamespace(),
            middlewareNamespace: $module->middlewareNamespace(),
            actionNamespace: $module->actionNamespace(),

            modelClass: $module->modelClass(),
            repositoryClass: $module->repositoryClass(),
            repositoryInterface: $module->repositoryInterface(),
            serviceClass: $module->serviceClass(),
            serviceInterface: $module->serviceInterface(),
            controllerClass: $module->controllerClass(),
            policyClass: $module->policyClass(),
            storeRequestClass: $module->storeRequestClass(),
            updateRequestClass: $module->updateRequestClass(),
            factoryClass: $module->factoryClass(),
            seederClass: $module->seederClass(),
            featureTestClass: $module->featureTestClass(),
            unitTestClass: $module->unitTestClass(),
            observerClass: $module->observerClass(),
            permissionClass: $module->permissionClass(),
            middlewareClass: $module->middlewareClass(),
            actionClass: $module->actionClass(),

            modelPath: $module->modelPath(),
            migrationPath: $module->migrationPath(),
            repositoryPath: $module->repositoryPath(),
            repositoryInterfacePath: $module->repositoryInterfacePath(),
            servicePath: $module->servicePath(),
            serviceInterfacePath: $module->serviceInterfacePath(),
            controllerPath: $module->controllerPath(),
            policyPath: $module->policyPath(),
            requestPath: $module->requestPath(),
            factoryPath: $module->factoryPath(),
            viewPath: $module->viewPath(),
            routePath: $module->routePath(),
            seederPath: $module->seederPath(),
            featureTestPath: $module->featureTestPath(),
            unitTestPath: $module->unitTestPath(),
            observerPath: $module->observerPath(),
            moduleManifestPath: $module->moduleManifestPath(),
            middlewarePath: $module->middlewarePath(),
            permissionPath: $module->permissionPath(),
            actionPath: $module->actionPath(),

            routePrefix: $module->routePrefix(),
            routeName: $module->routeName(),
            viewPrefix: $module->viewPrefix(),

            columns: $module->columns(),
            options: $module->options(),

            timestamps: $module->timestamps(),
            softDeletes: $module->softDeletes(),
            uuid: $module->uuid(),
            api: $module->api(),
            tests: $module->tests(),
            permissions: $module->permissions(),
            menu: $module->menu(),
            icon: $module->icon(),

            security: $module->security(),
            permissionMatrix: $module->permissionMatrix(),
            navigation: $module->navigation(),
        );
    }
}
