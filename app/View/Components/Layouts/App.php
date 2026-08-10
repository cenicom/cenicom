<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


final class App extends Component
{
    public function __construct(
        private readonly NavigationServiceInterface $navigationService,
        private readonly IdentityInterface $identity,
    ) {
    }

    public function render(): View
    {
        return view(
            'components.layouts.app',
            [
                'navigationTree' => $this->navigationService->tree(
                    $this->identity
                ),
            ]
        );
    }
}

