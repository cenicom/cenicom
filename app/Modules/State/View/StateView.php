<?php

declare(strict_types=1);

namespace App\Modules\State\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class StateView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'states',
            'app/Modules/State/Resources/Views',
        );
    }
}
