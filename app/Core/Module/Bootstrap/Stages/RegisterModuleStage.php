<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use RuntimeException;
use Throwable;

/**
 * Registers the validated module definition into the module registry.
 */
final class RegisterModuleStage implements ModuleBootstrapStageInterface
{
    public function __construct(
        private readonly ModuleRegistryInterface $registry,
    ) {
    }

    public function process(ModuleBootstrapContext $context): void
    {
        if ($context->hasException()) {
            return;
        }

        try {

            if (! $context->hasDefinition()) {
                throw new RuntimeException(
                    'Module definition has not been created.'
                );
            }

            $definition = $context->definition();

            if ($definition === null) {
                throw new RuntimeException(
                    'Module definition is null.'
                );
            }

            $this->registry->register($definition);

        } catch (Throwable $exception) {

            $context->setException($exception);
        }
    }
}
