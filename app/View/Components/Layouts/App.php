<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use Illuminate\View\Component;
use Illuminate\View\View;

final class App extends Component
{
    public function __construct(
        private readonly NavigationServiceInterface $navigationService
    ) {
    }


    public function render(): View
    {
        return view(
            'components.layouts.app',
            [
                'navigationTree' => $this->navigationService->tree(),
            ]
        );
    }
}
