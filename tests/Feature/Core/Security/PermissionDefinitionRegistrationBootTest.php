<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Tests\TestCase;

final class PermissionDefinitionRegistrationBootTest extends TestCase
{
    /**
     * Invocaciones observadas a register(), por clase.
     *
     * @var array<class-string, int>
     */
    private array $registerCalls = [];

    /**
     * Clases instrumentadas durante la resolución.
     *
     * @var array<class-string, true>
     */
    private array $instrumented = [];

    public function createApplication()
    {
        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $app->booting(function (Application $app): void {
            $app->beforeResolving(
                function (
                    mixed $abstract,
                    array $parameters,
                    Application $container,
                ): void {
                    if (
                        ! is_string($abstract)
                        || ! is_subclass_of(
                            $abstract,
                            PermissionDefinitionInterface::class,
                        )
                    ) {
                        return;
                    }

                    if (isset($this->instrumented[$abstract])) {
                        return;
                    }

                    $this->instrumented[$abstract] = true;

                    $container->extend(
                        $abstract,
                        function (mixed $instance) use ($abstract): mixed {
                            if (
                                ! $instance instanceof PermissionDefinitionInterface
                            ) {
                                return $instance;
                            }

                            return new class(
                                $instance,
                                function () use ($abstract): void {
                                    $this->registerCalls[$abstract] =
                                        ($this->registerCalls[$abstract] ?? 0) + 1;
                                },
                            ) implements PermissionDefinitionInterface {
                                public function __construct(
                                    private PermissionDefinitionInterface $inner,
                                    private \Closure $onRegister,
                                ) {
                                }

                                public function register(
                                    PermissionRegistrarInterface $permissions,
                                ): void {
                                    ($this->onRegister)();

                                    $this->inner->register($permissions);
                                }
                            };
                        },
                    );
                },
            );
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function test_each_enabled_permission_definition_registers_exactly_once_during_boot(): void
    {
        $definitions = app(PermissionDefinitionRegistryInterface::class)
            ->definitions();

        $this->assertNotEmpty(
            $definitions,
            'No se encontraron definiciones de permisos para verificar.',
        );

        $expectedDefinitions = array_values(
            array_unique($definitions),
        );

        sort($expectedDefinitions);

        $this->assertCount(
            count($expectedDefinitions),
            $definitions,
            'El registro contiene definiciones de permisos duplicadas.',
        );

        $instrumented = array_keys($this->instrumented);
        sort($instrumented);

        $missingInstrumentation = array_values(
            array_diff($expectedDefinitions, $instrumented),
        );

        $this->assertSame(
            [],
            $missingInstrumentation,
            'Hay definiciones de permisos sin instrumentar.',
        );

        $observedCalls = array_keys($this->registerCalls);
        sort($observedCalls);

        $this->assertSame(
            $expectedDefinitions,
            $observedCalls,
            'Las llamadas observadas no coinciden con las definiciones registradas.',
        );

        foreach ($expectedDefinitions as $definition) {
            $this->assertSame(
                1,
                $this->registerCalls[$definition] ?? 0,
                sprintf(
                    'La definición %s debe invocar register() exactamente una vez.',
                    $definition,
                ),
            );
        }

        $permissionRegistry = app(PermissionRegistryInterface::class);

        $permissions = $permissionRegistry->all();

        $permissionNames = array_map(
            static fn ($permission): string => $permission->name,
            $permissions,
        );

        sort($permissionNames);

        $expectedPermissionNames = [
            'campuses.create',
            'campuses.delete',
            'campuses.update',
            'campuses.view',
            'institutions.create',
            'institutions.delete',
            'institutions.update',
            'institutions.view',
            'inventory.categories.view',
            'inventory.movements.view',
            'inventory.products.view',
        ];

        sort($expectedPermissionNames);

        $this->assertSame(
            $expectedPermissionNames,
            $permissionNames,
            'El catálogo final no coincide exactamente con los 11 permisos esperados.',
        );

        $this->assertCount(
            count(array_unique($permissionNames)),
            $permissionNames,
            'El catálogo final contiene nombres de permisos duplicados.',
        );
    }
}
