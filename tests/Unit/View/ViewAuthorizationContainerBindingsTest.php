<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use App\View\Contracts\ViewAuthorizationInterface;
use App\View\ViewAuthorization;
use Tests\TestCase;

final class ViewAuthorizationContainerBindingsTest extends TestCase
{
    public function test_view_authorization_interface_resolves_to_view_authorization(): void
    {
        $authorization = $this->app->make(
            ViewAuthorizationInterface::class,
        );

        self::assertInstanceOf(
            ViewAuthorization::class,
            $authorization,
        );
    }

    public function test_view_authorization_is_singleton(): void
    {
        $first = $this->app->make(
            ViewAuthorizationInterface::class,
        );

        $second = $this->app->make(
            ViewAuthorizationInterface::class,
        );

        self::assertSame(
            $first,
            $second,
        );
    }

    public function test_view_authorization_resolves_through_contract(): void
    {
        $authorization = $this->app->make(
            ViewAuthorizationInterface::class,
        );

        self::assertInstanceOf(
            ViewAuthorizationInterface::class,
            $authorization,
        );
    }
}
