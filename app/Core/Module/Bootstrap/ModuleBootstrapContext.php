<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;


use App\Core\Module\Diagnostics\FailureTrace;
use App\Core\Module\DTO\ModuleDefinition;
use Throwable;

/**
 * Shared context used during the bootstrap pipeline execution.
 *
 * Every bootstrap stage reads and writes information through this object,
 * allowing the pipeline to coordinate the complete lifecycle of a module
 * without coupling the stages together.
 */
final class ModuleBootstrapContext
{
    /**
     * Indicates that the module has been skipped during bootstrap.
     */
    private bool $skipped = false;

    /**
     * Indicates whether the module was registered during
     * the current bootstrap execution.
     */
    private bool $moduleRegistered = false;

    private readonly ModuleBootstrapDiagnostics $diagnostics;

    private FailureTrace $failureTrace;

    /**
     * Creates a new bootstrap context.
     */
    public function __construct(
        private readonly string $manifestPath,

    ) {
        $this->diagnostics =
            new ModuleBootstrapDiagnostics();

        $this->diagnostics
            ->setManifestPath($manifestPath);

        $this->failureTrace = new FailureTrace();
    }

    public function failureTrace(): FailureTrace
    {
        return $this->failureTrace;
    }

    /**
     * Module definition produced by the factory.
     */
    private ?ModuleDefinition $definition = null;

    /**
     * Exception captured during the pipeline execution.
     */
    private ?Throwable $exception = null;

    /**
     * Returns the manifest path being processed.
     */
    public function manifestPath(): string
    {
        return $this->manifestPath;
    }

    public function diagnostics(): ModuleBootstrapDiagnostics
    {
        return $this->diagnostics;
    }

    /**
     * Stores the created module definition.
     */
    public function setDefinition(ModuleDefinition $definition): void
    {
        if ($this->definition !== null) {
            throw new \LogicException(
                'Module definition has already been assigned.'
            );
        }

        $this->definition = $definition;
    }

    /**
     * Returns the current module definition.
     */
    public function definition(): ?ModuleDefinition
    {
        return $this->definition;
    }

    /**
     * Indicates whether a definition has already been created.
     */
    public function hasDefinition(): bool
    {
        return $this->definition !== null;
    }

    /**
     * Stores the pipeline exception.
     */
    public function setException(Throwable $exception): void
    {
        if ($this->exception !== null) {
            return;
        }

        $this->exception = $exception;

        $this->diagnostics
            ->setException($exception);
    }

    /**
     * Returns the captured exception.
     */
    public function exception(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * Indicates whether the pipeline contains an exception.
     */
    public function hasException(): bool
    {
        return $this->exception !== null;
    }

    /**
     * Marks the current module as skipped.
     *
     * Disabled modules are not failures.
     * They are intentionally excluded from bootstrap execution.
     */
    public function markSkipped(): void
    {
        $this->skipped = true;
    }

    /**
     * Marks the module as successfully registered.
     */
    public function markModuleRegistered(): void
    {
        $this->moduleRegistered = true;
    }

    public function isModuleRegistered(): bool
    {
        return $this->moduleRegistered;
    }

    /**
     * Indicates whether the module was registered during
     * the current bootstrap execution.
     */
    public function wasModuleRegistered(): bool
    {
        return $this->moduleRegistered;
    }

    /**
     * Indicates whether the module was skipped.
     */
    public function isSkipped(): bool
    {
        return $this->skipped;
    }

    /**
     * Clears the stored exception.
     *
     * Reserved for future recovery stages.
     */
    public function clearException(): void
    {
        $this->exception = null;

        $this->diagnostics->clearException();
    }
}
