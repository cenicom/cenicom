<?php

declare(strict_types=1);

namespace Tests\Integration\Core\Module;

use App\Core\Module\Events\ModuleBooted;
use App\Core\Module\Events\ModuleBooting;
use App\Core\Module\Events\ModuleDiscovered;
use App\Core\Module\Events\ModuleFailed;
use App\Core\Module\Events\ModuleRegistered;
use App\Core\Module\Events\ModuleRunning;
use App\Core\Module\Events\ModuleStopped;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use App\Core\Module\Lifecycle\ModuleState;
use Tests\Fakes\FakeEventDispatcher;
use Tests\TestCase;

final class ModuleLifecycleEventsIntegrationTest extends TestCase
{
    private FakeEventDispatcher $events;

    private ModuleLifecycleManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new FakeEventDispatcher();

        $this->manager = new ModuleLifecycleManager(
            $this->events
        );
    }

    public function test_it_dispatches_module_discovered_event(): void
    {
        $this->manager->discovered('Inventory');

        $this->assertSame(
            ModuleState::DISCOVERED,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleDiscovered::class
        );
    }

    public function test_it_dispatches_module_registered_event(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');

        $this->assertSame(
            ModuleState::REGISTERED,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleRegistered::class
        );
    }

    public function test_it_dispatches_module_booting_event(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');
        $this->manager->booting('Inventory');

        $this->assertSame(
            ModuleState::BOOTING,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleBooting::class
        );
    }

    public function test_it_dispatches_module_booted_event(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');
        $this->manager->booting('Inventory');
        $this->manager->booted('Inventory');

        $this->assertSame(
            ModuleState::BOOTED,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleBooted::class
        );
    }

    public function test_it_dispatches_module_running_event(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');
        $this->manager->booting('Inventory');
        $this->manager->booted('Inventory');
        $this->manager->running('Inventory');

        $this->assertSame(
            ModuleState::RUNNING,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleRunning::class
        );
    }

    public function test_it_dispatches_module_stopped_event(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');
        $this->manager->booting('Inventory');
        $this->manager->booted('Inventory');
        $this->manager->running('Inventory');
        $this->manager->stopped('Inventory');

        $this->assertSame(
            ModuleState::STOPPED,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleStopped::class
        );
    }

    public function test_it_dispatches_module_failed_event(): void
    {
        $this->manager->discovered('Inventory');

        $this->manager->failed('Inventory');

        $this->assertSame(
            ModuleState::FAILED,
            $this->manager->state('Inventory')
        );

        $this->events->assertDispatched(
            ModuleFailed::class
        );
    }

    public function test_it_registers_only_one_lifecycle_per_module(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->registered('Inventory');

        $this->assertTrue(
            $this->manager->has('Inventory')
        );

        $this->assertSame(
            1,
            $this->manager->count()
        );
    }

    public function test_it_clears_all_registered_lifecycles(): void
    {
        $this->manager->discovered('Inventory');
        $this->manager->discovered('Students');

        $this->assertSame(
            2,
            $this->manager->count()
        );

        $this->manager->clear();

        $this->assertSame(
            0,
            $this->manager->count()
        );
    }
}
