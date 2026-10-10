<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Tests\TestCase;

final class NavigationDefinitionRegistrationBootTest extends TestCase
{
    /**
     * Invocaciones observadas a register(), por clase.
     *
     * @var array<class-string, int>
     */
    private array $registerCalls = [];

    /**
     * Clases para las que se instaló la instrumentación.
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
                            NavigationDefinitionInterface::class,
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
                                ! $instance instanceof NavigationDefinitionInterface
                            ) {
                                return $instance;
                            }

                            return new class(
                                $instance,
                                function () use ($abstract): void {
                                    $this->registerCalls[$abstract] =
                                        ($this->registerCalls[$abstract] ?? 0) + 1;
                                },
                            ) implements NavigationDefinitionInterface {
                                public function __construct(
                                    private NavigationDefinitionInterface $inner,
                                    private \Closure $onRegister,
                                ) {
                                }

                                public function register(
                                    NavigationRegistrarInterface $navigation,
                                ): void {
                                    ($this->onRegister)();

                                    $this->inner->register($navigation);
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

    public function test_each_enabled_navigation_definition_registers_exactly_once_during_boot(): void
    {
        $definitions = app(NavigationDefinitionRegistry::class)
            ->definitions();

        $this->assertNotEmpty(
            $definitions,
            'No se encontraron definiciones de navegación para verificar.',
        );

        $expected = array_values(array_unique($definitions));
        sort($expected);

        $this->assertCount(
            count($expected),
            $definitions,
            'El registro contiene definiciones duplicadas.',
        );

        $instrumented = array_keys($this->instrumented);
        sort($instrumented);

        $missingInstrumentation = array_values(
            array_diff($expected, $instrumented),
        );

        $this->assertSame(
            [],
            $missingInstrumentation,
            'Hay definiciones habilitadas que no fueron instrumentadas.',
        );

        $observedCalls = array_keys($this->registerCalls);
        sort($observedCalls);

        $this->assertSame(
            $expected,
            $observedCalls,
            'Las llamadas observadas no coinciden exactamente con las definiciones habilitadas.',
        );

        foreach ($expected as $definition) {
            $this->assertSame(
                1,
                $this->registerCalls[$definition] ?? 0,
                sprintf(
                    'La definición %s debe invocar register() exactamente una vez.',
                    $definition,
                ),
            );
        }
    }
}
