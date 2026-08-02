<?php

declare(strict_types=1);

namespace App\Core\Navigation\View;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\Contracts\NavigationActiveResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\View\View;

final readonly class NavigationViewComposer
{
    public function __construct(
        private NavigationServiceInterface $navigationService,
        private NavigationActiveResolverInterface $activeResolver,
        private IdentityInterface $identity,
    ) {
    }

    public function compose(View $view): void
    {
        if (array_key_exists(
            'navigation',
            $view->getData()
        )) {
            return;
        }

        $tree = $this->navigationService->tree(
            $this->identity
        );

        $tree = $this->activeResolver->resolve(
            $tree
        );

        $view->with(
            'navigation',
            $tree
        );
    }
}
