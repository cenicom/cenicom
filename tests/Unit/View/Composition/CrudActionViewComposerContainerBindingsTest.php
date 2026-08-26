<?php

declare(strict_types=1);

namespace Tests\Unit\View\Composition;

use App\View\Composition\Contracts\CrudActionViewComposerInterface;
use App\View\Composition\CrudActionViewComposer;
use App\View\Contracts\ViewAuthorizationInterface;
use Tests\TestCase;

final class CrudActionViewComposerContainerBindingsTest extends TestCase
{
    public function test_composer_interface_resolves_to_composer(): void
    {
        $composer = $this->app->make(
            CrudActionViewComposerInterface::class
        );

        self::assertInstanceOf(
            CrudActionViewComposer::class,
            $composer,
        );
    }

    public function test_composer_is_singleton(): void
    {
        $first = $this->app->make(
            CrudActionViewComposerInterface::class
        );

        $second = $this->app->make(
            CrudActionViewComposerInterface::class
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_composer_resolves_view_authorization_dependency(): void
    {
        $composer = $this->app->make(
            CrudActionViewComposerInterface::class
        );

        self::assertInstanceOf(
            CrudActionViewComposer::class,
            $composer,
        );

        $authorization = $this->app->make(
            ViewAuthorizationInterface::class
        );

        self::assertInstanceOf(
            ViewAuthorizationInterface::class,
            $authorization,
        );
    }

    public function test_composer_can_be_resolved_through_contract(): void
    {
        $composer = $this->app->make(
            CrudActionViewComposerInterface::class
        );

        self::assertInstanceOf(
            CrudActionViewComposerInterface::class,
            $composer,
        );
    }
}
