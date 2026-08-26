<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Registry;

use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\ViewRegistry;
use PHPUnit\Framework\TestCase;

final class ViewRegistryTest extends TestCase
{
    public function test_registry_implements_contract(): void
    {
        $registry = new ViewRegistry();

        self::assertInstanceOf(
            ViewRegistryInterface::class,
            $registry,
        );
    }

    public function test_register_stores_namespace_and_path(): void
    {
        $registry = new ViewRegistry();

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        self::assertSame(
            'app/Modules/Institution/resources/views',
            $registry->path('institutions'),
        );
    }

    public function test_path_returns_null_for_unknown_namespace(): void
    {
        $registry = new ViewRegistry();

        self::assertNull(
            $registry->path('unknown'),
        );
    }

    public function test_all_returns_registered_views(): void
    {
        $registry = new ViewRegistry();

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        $registry->register(
            'inventory',
            'app/Modules/Inventory/resources/views',
        );

        self::assertSame(
            [
                'institutions' => 'app/Modules/Institution/resources/views',
                'inventory' => 'app/Modules/Inventory/resources/views',
            ],
            $registry->all(),
        );
    }

    public function test_register_rejects_duplicate_namespace(): void
    {
        $registry = new ViewRegistry();

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'View namespace [institutions] is already registered.'
        );

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/custom-views',
        );
    }

    public function test_clear_removes_registered_views(): void
    {
        $registry = new ViewRegistry();

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        $registry->clear();

        self::assertSame(
            [],
            $registry->all(),
        );

        self::assertNull(
            $registry->path('institutions'),
        );
    }

    public function test_register_rejects_duplicate_namespace_even_when_path_is_identical(): void
    {
        $registry = new ViewRegistry();

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'View namespace [institutions] is already registered.'
        );

        $registry->register(
            'institutions',
            'app/Modules/Institution/resources/views',
        );
    }
}
