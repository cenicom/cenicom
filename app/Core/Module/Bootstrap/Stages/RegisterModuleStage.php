<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use RuntimeException;
use Throwable;

final class RegisterModuleStage implements ModuleBootstrapStageInterface
{
    public function __construct(
        private readonly ModuleRegistryInterface $registry,
    ) {}

    public function process(ModuleBootstrapContext $context): void
    {
        if ($context->hasException()) {
            return;
        }

        $definition = $context->definition();

        if ($definition === null) {
            $context->setException(
                new RuntimeException(
                    'Cannot register module without definition.'
                )
            );

            return;
        }

        try {
            $this->registry->register($definition);

            $context->markModuleRegistered();
        } catch (Throwable $exception) {
            $context->setException($exception);
        }
    }
}
