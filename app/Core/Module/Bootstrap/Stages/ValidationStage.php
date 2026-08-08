<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use RuntimeException;
use Throwable;

/**
 * Validates the module definition before continuing the bootstrap pipeline.
 *
 * This stage is responsible only for validating the shared
 * ModuleBootstrapContext and the ModuleDefinition previously created.
 */
final class ValidationStage implements ModuleBootstrapStageInterface
{
    private bool $skipped = false;

    public function markSkipped(): void
    {
        $this->skipped = true;
    }

    public function isSkipped(): bool
    {
        return $this->skipped;
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

            if (! $definition->enabled) {
                $context->markSkipped();

                return;
            }

            // -----------------------------------------------------------------
            // Future validations
            // -----------------------------------------------------------------
            //
            // - Manifest integrity
            // - Required providers
            // - Duplicate module names
            // - Dependency validation
            // - Version compatibility
            // - Signature verification
            //

        } catch (Throwable $exception) {

            $context->setException($exception);
        }
    }
}
