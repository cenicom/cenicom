<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\DTO;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class NavigationNodeDataTest extends TestCase
{
    public function test_creates_navigation_node(): void
    {
        $node = new NavigationNodeData(
            id: 'dashboard',
            label: 'Dashboard',
            type: NavigationNodeType::ITEM,
            icon: 'bi-house',
            route: 'dashboard',
            order: 10,
        );

        $this->assertSame(
            'dashboard',
            $node->id()
        );

        $this->assertSame(
            'Dashboard',
            $node->label()
        );

        $this->assertSame(
            NavigationNodeType::ITEM,
            $node->type()
        );

        $this->assertSame(
            'bi-house',
            $node->icon()
        );

        $this->assertSame(
            'dashboard',
            $node->route()
        );

        $this->assertSame(
            10,
            $node->order()
        );
    }

    public function test_detects_leaf_node(): void
    {
        $node = new NavigationNodeData(
            id: 'dashboard',
            label: 'Dashboard',
            type: NavigationNodeType::ITEM,
        );

        $this->assertTrue(
            $node->isLeaf()
        );

        $this->assertFalse(
            $node->hasChildren()
        );

        $this->assertSame(
            [],
            $node->children()
        );
    }

    public function test_detects_node_with_children(): void
    {
        $child = new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
        );

        $parent = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            children: [$child],
        );

        $this->assertTrue(
            $parent->hasChildren()
        );

        $this->assertFalse(
            $parent->isLeaf()
        );

        $this->assertCount(
            1,
            $parent->children()
        );

        $this->assertSame(
            $child,
            $parent->children()[0]
        );
    }

    public function test_with_children_returns_new_instance(): void
    {
        $node = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
        );

        $child = new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
        );

        $updated = $node->withChildren([
            $child,
        ]);

        $this->assertNotSame(
            $node,
            $updated
        );

        $this->assertFalse(
            $node->hasChildren()
        );

        $this->assertTrue(
            $updated->hasChildren()
        );

        $this->assertCount(
            1,
            $updated->children()
        );

        $this->assertSame(
            NavigationNodeType::GROUP,
            $updated->type()
        );
    }

    public function test_with_state_updates_navigation_flags(): void
    {
        $node = new NavigationNodeData(
            id: 'dashboard',
            label: 'Dashboard',
            type: NavigationNodeType::ITEM,
        );

        $updated = $node->withState(
            current: true,
            active: true,
            ancestor: false,
            expanded: true,
        );

        $this->assertNotSame(
            $node,
            $updated
        );

        $this->assertTrue(
            $updated->isCurrent()
        );

        $this->assertTrue(
            $updated->isActive()
        );

        $this->assertFalse(
            $updated->isAncestor()
        );

        $this->assertTrue(
            $updated->isExpanded()
        );

        $this->assertFalse(
            $node->isCurrent()
        );

        $this->assertFalse(
            $node->isActive()
        );
    }

    public function test_href_returns_explicit_url(): void
    {
        $node = new NavigationNodeData(
            id: 'documentation',
            label: 'Documentación',
            type: NavigationNodeType::ITEM,
            url: 'https://example.com/docs',
        );

        $this->assertSame(
            'https://example.com/docs',
            $node->href()
        );
    }


    public function test_href_returns_hash_when_route_does_not_exist(): void
    {
        Route::shouldReceive('has')
            ->once()
            ->with('missing.route')
            ->andReturn(false);

        $node = new NavigationNodeData(
            id: 'missing',
            label: 'Missing',
            type: NavigationNodeType::ITEM,
            route: 'missing.route',
        );

        $this->assertSame(
            '#',
            $node->href()
        );
    }
}
