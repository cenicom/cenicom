<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Contracts\Module\ModuleDefinitionFactoryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use Throwable;

final class CreateDefinitionStage implements ModuleBootstrapStageInterface
{
    public function __construct(
        private readonly ModuleDefinitionFactoryInterface $factory,
    ) {
    }

    public function process(ModuleBootstrapContext $context): void
    {

        if ($context->hasException()) {
            return;
        }

        try {

            $definition = $this->factory->create(
                $context->manifestPath()
            );

            $context->setDefinition(
                $definition
            );

        } catch (Throwable $exception) {

            $context->setException(
                $exception
            );
        }
    }
}
