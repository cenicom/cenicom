<?php

declare(strict_types=1);

namespace App\Modules\Institution\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class InstitutionView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'institution',
            'app/Modules/Institution/resources/views',
        );
    }
}
