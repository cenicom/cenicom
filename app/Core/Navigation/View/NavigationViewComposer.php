<?php

declare(strict_types=1);

namespace App\Core\Navigation\View;

use App\Core\Navigation\Contracts\NavigationServiceInterface;

use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\View\View;

final class NavigationViewComposer
{
    public function __construct(
        private readonly NavigationServiceInterface $navigation,
        private readonly IdentityInterface $identity,
    ) {
    }

    public function compose(View $view): void
    {
        $view->with(
            'navigation',
            $this->navigation->tree(
                $this->identity
            )
        );
    }
}
