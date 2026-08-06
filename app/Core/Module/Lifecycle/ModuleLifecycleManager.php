<?php

declare(strict_types=1);

namespace App\Core\Module\Lifecycle;


use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Module\Events\ModuleBooted;
use App\Core\Module\Events\ModuleBooting;
use App\Core\Module\Events\ModuleDiscovered;
use App\Core\Module\Events\ModuleFailed;
use App\Core\Module\Events\ModuleRegistered;
use App\Core\Module\Events\ModuleRunning;
use App\Core\Module\Events\ModuleStopped;
use InvalidArgumentException;

/**
 * Manages lifecycle instances for all registered modules.
 *
 * This class acts as the central registry for module lifecycles,
 * providing creation, lookup and state transition operations.
 */
final class ModuleLifecycleManager
{
    /**
     * Registered module lifecycles.
     *
     * @var array<string, ModuleLifecycle>
     */
    private array $lifecycles = [];

    public function __construct(
        private readonly EventDispatcherInterface $events,
    ) {}

    /**
     * Creates a lifecycle for the given module.
     *
     * If the lifecycle already exists, the existing instance is returned.
     */
    public function create(string $module): ModuleLifecycle
    {
        return $this->lifecycles[$module]
            ??= new ModuleLifecycle();
    }

    /**
     * Returns the lifecycle for a module.
     *
     * @throws InvalidArgumentException
     */
    public function lifecycle(string $module): ModuleLifecycle
    {
        if (! isset($this->lifecycles[$module])) {
            throw new InvalidArgumentException(sprintf(
                'Module lifecycle [%s] is not registered.',
                $module
            ));
        }

        return $this->lifecycles[$module];
    }

    /**
     * Determines whether a lifecycle exists.
     */
    public function has(string $module): bool
    {
        return isset($this->lifecycles[$module]);
    }

    /**
     * Returns the current state of a module.
     *
     * @throws InvalidArgumentException
     */
    public function state(string $module): ModuleState
    {
        return $this->lifecycle($module)->state();
    }

    /**
     * Marks a module as discovered.
     */
    public function discovered(string $module): void
    {
        $this->create($module)->discovered();

        $this->events->dispatch(
            new ModuleDiscovered($module)
        );
    }

    /**
     * Marks a module as registered.
     */
    public function registered(string $module): void
    {
        $this->create($module)->registered();

        $this->events->dispatch(
            new ModuleRegistered($module)
        );
    }

    /**
     * Marks a module as booting.
     */
    public function booting(string $module): void
    {
        $this->create($module)->booting();

        $this->events->dispatch(
            new ModuleBooting($module)
        );
    }

    /**
     * Marks a module as booted.
     */
    public function booted(string $module): void
    {
        $this->create($module)->booted();

        $this->events->dispatch(
            new ModuleBooted($module)
        );
    }

    /**
     * Marks a module as running.
     */
    public function running(string $module): void
    {
        $this->create($module)->running();

        $this->events->dispatch(
            new ModuleRunning($module)
        );
    }

    /**
     * Marks a module as stopped.
     */
    public function stopped(string $module): void
    {
        $this->create($module)->stopped();

        $this->events->dispatch(
            new ModuleStopped($module)
        );
    }

    /**
     * Marks a module as failed.
     */
    public function failed(string $module): void
    {
        $this->create($module)->failed();

        $this->events->dispatch(
            new ModuleFailed($module)
        );
    }

    /**
     * Returns all registered lifecycles.
     *
     * @return array<string, ModuleLifecycle>
     */
    public function all(): array
    {
        return $this->lifecycles;
    }

    /**
     * Returns the number of registered lifecycles.
     */
    public function count(): int
    {
        return count($this->lifecycles);
    }

    /**
     * Removes all registered lifecycles.
     */
    public function clear(): void
    {
        $this->lifecycles = [];
    }
}
