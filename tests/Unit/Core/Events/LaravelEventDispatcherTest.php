<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Events;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Events\LaravelEventDispatcher;
use App\Core\Module\Events\ModuleRunning;
use Illuminate\Support\Facades\Event;
use stdClass;
use Tests\TestCase;

final class LaravelEventDispatcherTest extends TestCase
{
    private LaravelEventDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new LaravelEventDispatcher();
    }

    public function test_it_implements_event_dispatcher_interface(): void
    {
        $this->assertInstanceOf(
            EventDispatcherInterface::class,
            $this->dispatcher
        );
    }

    public function test_it_dispatches_module_events(): void
    {
        Event::fake();

        $event = new ModuleRunning('Inventory');

        $this->dispatcher->dispatch($event);

        Event::assertDispatched(
            ModuleRunning::class,
            static fn (ModuleRunning $dispatched): bool =>
                $dispatched->module() === 'Inventory'
        );
    }

    public function test_it_accepts_any_object(): void
    {
        Event::fake();

        $event = new stdClass();

        $this->dispatcher->dispatch($event);

        Event::assertDispatched(
            stdClass::class
        );
    }
}
