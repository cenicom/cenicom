<?php

declare(strict_types=1);

namespace App\Modules\Country\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class CountryView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'countries',
            'app/Modules/Country/Resources/Views',
        );
    }
}
