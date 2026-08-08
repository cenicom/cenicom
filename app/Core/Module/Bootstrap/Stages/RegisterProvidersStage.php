<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use RuntimeException;
use Throwable;

/**
 * Registers the service providers defined by a module.
 */
final class RegisterProvidersStage implements ModuleBootstrapStageInterface
{
    public function __construct(
        private readonly ModuleProviderRegistrarInterface $registrar,
    ) {}

    public function process(ModuleBootstrapContext $context): void
    {
        if ($context->hasException()) {
            return;
        }

        try {
            $definition = $context->definition();

            if ($definition === null) {
                throw new RuntimeException(
                    'Module definition has not been created.'
                );
            }

            $this->registrar->registerDefinition($definition);
        } catch (Throwable $exception) {
            $context->setException($exception);
        }
    }
}
