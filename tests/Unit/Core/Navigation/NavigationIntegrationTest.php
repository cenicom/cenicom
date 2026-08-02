<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\Registry\NavigationRegistry;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Resolver\NavigationActiveResolver;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

final class NavigationIntegrationTest extends TestCase
{
    private function createBuilderContext(): array
    {
        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $permissionResolver
            ->shouldReceive('canView')
            ->andReturn(true);

        return [
            $permissionResolver,
            $identity,
        ];
    }


    public function test_builds_complete_navigation_tree_from_registry(): void
    {
        $registry = new NavigationRegistry();

        $registrar = new NavigationRegistrar(
            $registry
        );

        $registrar
            ->group(
                new NavigationGroupData(
                    id: 'administration',
                    label: 'Administración',
                    icon: null,
                    order: 10,
                )
            )
            ->item(
                new NavigationItemData(
                    id: 'institutions',
                    label: 'Instituciones',
                    route: 'institutions.index',
                    group: 'administration',
                )
            );

        [
            $permissionResolver,
            $identity,
        ] = $this->createBuilderContext();

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build(
            $identity
        );

        $this->assertCount(
            1,
            $tree->nodes()
        );

        $this->assertSame(
            'administration',
            $tree->nodes()[0]->id()
        );

        $this->assertCount(
            1,
            $tree->nodes()[0]->children()
        );
    }


    public function test_resolves_current_route_and_marks_parent_active(): void
    {
        Route::get(
            '/institutions',
            fn() => 'ok'
        )->name(
            'institutions.index'
        );

        $registry = new NavigationRegistry();

        $registrar = new NavigationRegistrar(
            $registry
        );

        $registrar
            ->group(
                new NavigationGroupData(
                    id: 'administration',
                    label: 'Administración',
                    icon: null,
                    order: 10,
                )
            )
            ->item(
                new NavigationItemData(
                    id: 'institutions',
                    label: 'Instituciones',
                    route: 'institutions.index',
                    group: 'administration',
                )
            );

        [
            $permissionResolver,
            $identity,
        ] = $this->createBuilderContext();

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build(
            $identity
        );

        $this->get(
            '/institutions'
        );

        $resolved = (new NavigationActiveResolver())
            ->resolve($tree);

        $group = $resolved->nodes()[0];

        $item = $group->children()[0];

        $this->assertTrue(
            $item->isCurrent()
        );

        $this->assertTrue(
            $item->isActive()
        );

        $this->assertTrue(
            $group->isActive()
        );

        $this->assertTrue(
            $group->isExpanded()
        );
    }
}
