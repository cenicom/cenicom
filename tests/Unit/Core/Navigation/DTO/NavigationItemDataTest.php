<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\DTO;

use App\Core\Navigation\DTO\NavigationItemData;
use Tests\TestCase;

final class NavigationItemDataTest extends TestCase
{
    public function test_creates_navigation_item_with_permission(): void
    {
        $item = new NavigationItemData(
            id: 'users',
            label: 'Usuarios',
            route: 'users.index',
            permission: 'users.view',
            icon: 'bi-people',
            order: 10,

        );

        $this->assertSame(
            'users',
            $item->id()
        );

        $this->assertSame(
            'users.view',
            $item->permission
        );
    }


    public function test_creates_public_navigation_item_without_permission(): void
    {
        $item = new NavigationItemData(
            id: 'dashboard',
            label: 'Dashboard',
            route: 'dashboard',
        );

        $this->assertNull(
            $item->permission
        );
    }


    public function test_preserves_navigation_item_data(): void
    {
        $item = new NavigationItemData(
            id: 'reports',
            label: 'Reportes',
            route: 'reports.index',
            permission: 'reports.view',
            icon: 'bi-file-text',
            order: 20,
            group: 'administration',
        );

        $this->assertSame(
            'Reportes',
            $item->label()
        );

        $this->assertSame(
            'reports.index',
            $item->route()
        );

        $this->assertSame(
            20,
            $item->order()
        );
    }
}
