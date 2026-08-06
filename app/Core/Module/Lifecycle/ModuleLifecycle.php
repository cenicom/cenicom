<?php

declare(strict_types=1);

namespace App\Core\Module\Lifecycle;

use LogicException;

/**
 * Manages the lifecycle state of a single module.
 *
 * This class is framework-agnostic and is responsible for validating
 * and applying state transitions during the lifetime of a module.
 */
final class ModuleLifecycle
{
    /**
     * Current lifecycle state.
     */
    private ModuleState $state;

    public function __construct()
    {
        $this->state = ModuleState::DISCOVERED;
    }

    /**
     * Returns the current lifecycle state.
     */
    public function state(): ModuleState
    {
        return $this->state;
    }

    /**
     * Marks the module as discovered.
     */
    public function discovered(): void
    {
        $this->transitionTo(ModuleState::DISCOVERED);
    }

    /**
     * Marks the module as registered.
     */
    public function registered(): void
    {
        $this->transitionTo(ModuleState::REGISTERED);
    }

    /**
     * Marks the module as booting.
     */
    public function booting(): void
    {
        $this->transitionTo(ModuleState::BOOTING);
    }

    /**
     * Marks the module as booted.
     */
    public function booted(): void
    {
        $this->transitionTo(ModuleState::BOOTED);
    }

    /**
     * Marks the module as running.
     */
    public function running(): void
    {
        $this->transitionTo(ModuleState::RUNNING);
    }

    /**
     * Marks the module as stopped.
     */
    public function stopped(): void
    {
        $this->transitionTo(ModuleState::STOPPED);
    }

    /**
     * Marks the module as failed.
     */
    public function failed(): void
    {
        $this->transitionTo(ModuleState::FAILED);
    }

    /**
     * Performs a validated state transition.
     *
     * @throws LogicException
     */
    private function transitionTo(ModuleState $next): void
    {
        if (! $this->canTransitionTo($next)) {
            throw new LogicException(sprintf(
                'Invalid module lifecycle transition from "%s" to "%s".',
                $this->state->value,
                $next->value
            ));
        }

        $this->state = $next;
    }

    /**
     * Determines whether the current state can transition
     * to the requested state.
     */
    private function canTransitionTo(ModuleState $next): bool
    {
        return match ($this->state) {
            ModuleState::DISCOVERED => in_array(
                $next,
                [
                    ModuleState::DISCOVERED,
                    ModuleState::REGISTERED,
                    ModuleState::FAILED,
                ],
                true
            ),

            ModuleState::REGISTERED => in_array(
                $next,
                [
                    ModuleState::BOOTING,
                    ModuleState::FAILED,
                ],
                true
            ),

            ModuleState::BOOTING => in_array(
                $next,
                [
                    ModuleState::BOOTED,
                    ModuleState::FAILED,
                ],
                true
            ),

            ModuleState::BOOTED => in_array(
                $next,
                [
                    ModuleState::RUNNING,
                    ModuleState::FAILED,
                ],
                true
            ),

            ModuleState::RUNNING => in_array(
                $next,
                [
                    ModuleState::STOPPED,
                    ModuleState::FAILED,
                ],
                true
            ),

            ModuleState::STOPPED => false,

            ModuleState::FAILED => false,
        };
    }
}
