<?php

declare(strict_types=1);

namespace App\Core\Navigation\View;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use Illuminate\View\View;

final readonly class NavigationViewComposer
{
    public function __construct(
        private NavigationServiceInterface $navigationService
    ) {
    }


    public function compose(View $view): void
    {
        $tree = $this->navigationService->tree();

        $view->with(
            'navigationTree',
            $this->navigationService->tree()
        );
    }
}
