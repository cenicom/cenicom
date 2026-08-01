<?php

declare(strict_types=1);

namespace Tests\Unit\Navigation\DTO;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use PHPUnit\Framework\TestCase;

final class NavigationTreeDataTest extends TestCase
{
    public function test_can_create_empty_tree(): void
    {
        $tree = new NavigationTreeData(
            nodes: [],
        );

        $this->assertSame([], $tree->nodes());
    }

    public function test_can_create_tree_with_single_node(): void
    {
        $node = $this->makeNode();

        $tree = new NavigationTreeData(
            nodes: [$node],
        );

        $this->assertCount(1, $tree->nodes());
        $this->assertSame($node, $tree->nodes()[0]);
    }

    public function test_can_create_tree_with_multiple_nodes(): void
    {
        $tree = new NavigationTreeData(
            nodes: [
                $this->makeNode('users'),
                $this->makeNode('roles'),
                $this->makeNode('permissions'),
            ],
        );

        $this->assertCount(3, $tree->nodes());
    }

    public function test_preserves_node_order(): void
    {
        $first = $this->makeNode('first');
        $second = $this->makeNode('second');

        $tree = new NavigationTreeData(
            nodes: [
                $first,
                $second,
            ],
        );

        $this->assertSame($first, $tree->nodes()[0]);
        $this->assertSame($second, $tree->nodes()[1]);
    }

    public function test_supports_nested_tree(): void
    {
        $child = $this->makeNode('child');

        $parent = $this->makeNode(
            id: 'parent',
            children: [$child],
        );

        $tree = new NavigationTreeData(
            nodes: [$parent],
        );

        $this->assertCount(1, $tree->nodes());

        $this->assertCount(
            1,
            $tree->nodes()[0]->children()
        );

        $this->assertSame(
            $child,
            $tree->nodes()[0]->children()[0]
        );
    }

    private function makeNode(
        string $id = 'node',
        array $children = [],
    ): NavigationNodeData {

        return new NavigationNodeData(
            id: $id,
            label: ucfirst($id),
            type: NavigationNodeType::ITEM,
            order: 1,
            children: $children,
        );
    }
}
