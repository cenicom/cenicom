<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;



use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\BindingWriter;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Registra automáticamente los bindings del módulo
 * dentro de config/cn-bindings.php.
 *
 * @package App\Core\Generator\Generators
 * @since 2.0.0
 */
final readonly class BindingGenerator implements GeneratorInterface
{
    public function __construct(
        private BindingWriter $bindings,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function supports(
        ModuleData $module,
    ): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(
        ModuleData $module,
    ): GeneratorResult {

        $result = new GeneratorResult();

        $this->register(
            $module->qualifiedRepositoryInterface(),
            $module->qualifiedRepository(),
            $result,
        );

        $this->register(
            $module->qualifiedServiceInterface(),
            $module->qualifiedService(),
            $result,
        );

        return $result;
    }

    /**
     * Registra un binding.
     */
    private function register(
        string $interface,
        string $implementation,
        GeneratorResult $result,
    ): void {

        if (
            $this->bindings->add(
                $interface,
                $implementation,
            )
        ) {

            $result->addCreated(
                config_path('cn-bindings.php')
            );

            return;
        }

        $result->addSkipped(
            config_path('cn-bindings.php')
        );
    }
}
