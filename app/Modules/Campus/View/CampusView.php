<?php

declare(strict_types=1);

namespace App\Modules\Campus\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class CampusView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'campuses',
            'app/Modules/Campus/Resources/Views',
        );
    }
}
