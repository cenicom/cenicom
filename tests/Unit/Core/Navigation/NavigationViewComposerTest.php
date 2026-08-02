<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\View;

use App\Core\Navigation\Contracts\NavigationActiveResolverInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationTreeData;

use App\Core\Navigation\View\NavigationViewComposer;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\View\View;
use PHPUnit\Framework\TestCase;

final class NavigationViewComposerTest extends TestCase
{
    public function test_composes_navigation_tree_into_view(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $tree = new NavigationTreeData([]);

        $service = $this->createMock(
            NavigationServiceInterface::class
        );

        $service
            ->expects($this->once())
            ->method('tree')
            ->with($identity)
            ->willReturn($tree);

        $resolver = $this->createMock(
            NavigationActiveResolverInterface::class
        );

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($tree)
            ->willReturn($tree);

        $view = $this->createMock(
            View::class
        );

        $view
            ->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $view
            ->expects($this->once())
            ->method('with')
            ->with(
                'navigation',
                $tree
            );

        $composer = new NavigationViewComposer(
            $service,
            $resolver,
            $identity
        );

        $composer->compose($view);
    }
}
