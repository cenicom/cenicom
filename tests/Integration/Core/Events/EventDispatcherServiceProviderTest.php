<?php

declare(strict_types=1);

namespace Tests\Integration\Core\Events;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Events\LaravelEventDispatcher;
use Tests\TestCase;

final class EventDispatcherServiceProviderTest extends TestCase
{
    public function test_it_resolves_the_event_dispatcher(): void
    {
        $dispatcher = app(EventDispatcherInterface::class);

        $this->assertInstanceOf(
            LaravelEventDispatcher::class,
            $dispatcher
        );
    }
}
