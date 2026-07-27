<?php

declare(strict_types=1);

namespace App\Core\Navigation\View;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;

use App\Core\Navigation\Resolver\NavigationActiveResolver;
use Illuminate\View\View;

final readonly class NavigationViewComposer
{

    public function compose(View $view): void
    {
        if ($view->getData()['navigation'] ?? null) {
            return;
        }

        $tree = app(NavigationBuilderInterface::class)
            ->build();

        $tree = app(NavigationActiveResolver::class)
            ->resolve($tree);

        $view->with(
            'navigation',
            $tree
        );
    }
}
