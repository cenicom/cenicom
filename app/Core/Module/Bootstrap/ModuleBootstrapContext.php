<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;


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
     * Creates a new bootstrap context.
     */
    public function __construct(
        private readonly string $manifestPath,
    ) {
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

    /**
     * Stores the created module definition.
     */
    public function setDefinition(ModuleDefinition $definition): void
    {
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
        $this->exception = $exception;
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
     * Clears the stored exception.
     *
     * Reserved for future recovery stages.
     */
    public function clearException(): void
    {
        $this->exception = null;
    }
}
