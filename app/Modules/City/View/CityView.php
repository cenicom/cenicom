<?php

declare(strict_types=1);

namespace App\Modules\City\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class CityView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'cities',
            'app/Modules/City/Resources/Views',
        );
    }
}
