<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\DTO\PermissionMatrix;

use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\RegisterPermissionsStep;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

use Tests\TestCase;

final class RegisterPermissionsStepTest extends TestCase
{

    public function test_step_implements_pipeline_contract(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $this->assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_registers_permissions(): void
    {
        $permission = new PermissionDefinition(
            name: 'Users',
            action: 'view',
            permission: 'users.view',
            group: 'Users',
            description: 'View users',
        );

        $matrix = new PermissionMatrix([
            $permission,
        ]);

        $module = $this->createMock(ModuleData::class);

        $module
            ->method('permissionMatrix')
            ->willReturn($matrix);

        $result = new GeneratorResult();

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registrar
            ->expects($this->once())
            ->method('register')
            ->with(
                'users.view',
                'View users',
                'Users',
            )
            ->willReturn(
                new \App\Core\Security\Permissions\DTO\PermissionDefinition(
                    name: 'users.view',
                    description: 'View users',
                    module: 'Users',
                )
            );

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $returned = $step->handle(
            $module,
            $result,
            static fn(
                ModuleData $module,
                GeneratorResult $result,
            ) => $result,
        );

        $this->assertSame(
            $result,
            $returned
        );
    }

    public function test_skips_registration_when_matrix_is_empty(): void
    {
        $module = $this->createMock(ModuleData::class);

        $module
            ->method('permissionMatrix')
            ->willReturn(
                new PermissionMatrix([])
            );

        $result = new GeneratorResult();

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registrar
            ->expects($this->never())
            ->method('register');

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $returned = $step->handle(
            $module,
            $result,
            static fn(
                ModuleData $module,
                GeneratorResult $result,
            ) => $result,
        );

        $this->assertSame(
            $result,
            $returned
        );
    }

    public function test_pipeline_continues_after_registration(): void
    {
        $permission = new PermissionDefinition(
            name: 'Users',
            action: 'view',
            permission: 'users.view',
            group: 'Users',
            description: 'View users'
        );

        $matrix = new PermissionMatrix([
            $permission,
        ]);

        $module = $this->createMock(ModuleData::class);

        $module
            ->method('permissionMatrix')
            ->willReturn($matrix);

        $result = new GeneratorResult();

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registrar
            ->method('register')
            ->willReturn(
                new \App\Core\Security\Permissions\DTO\PermissionDefinition(
                    name: 'users.view',
                )
            );

        $called = false;

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $step->handle(
            $module,
            $result,
            static function (
                ModuleData $module,
                GeneratorResult $result,
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            },
        );

        $this->assertTrue($called);
    }

    public function test_preserves_same_module_instance(): void
    {
        $module = $this->createMock(ModuleData::class);

        $module
            ->method('permissionMatrix')
            ->willReturn(
                new PermissionMatrix([])
            );

        $result = new GeneratorResult();

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $received = null;

        $step->handle(
            $module,
            $result,
            static function (
                ModuleData $module,
                GeneratorResult $result,
            ) use (&$received): GeneratorResult {

                $received = $module;

                return $result;
            },
        );

        $this->assertSame(
            $module,
            $received
        );
    }

    public function test_preserves_same_generator_result(): void
    {
        $module = $this->createMock(ModuleData::class);

        $module
            ->method('permissionMatrix')
            ->willReturn(
                new PermissionMatrix([])
            );

        $result = new GeneratorResult();

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $step = new RegisterPermissionsStep(
            $registrar
        );

        $received = null;

        $step->handle(
            $module,
            $result,
            static function (
                ModuleData $module,
                GeneratorResult $result,
            ) use (&$received): GeneratorResult {

                $received = $result;

                return $result;
            },
        );

        $this->assertSame(
            $result,
            $received
        );
    }
}
