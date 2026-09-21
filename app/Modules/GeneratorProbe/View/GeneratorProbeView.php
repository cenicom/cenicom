<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class GeneratorProbeView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'generator_probes',
            'app/Modules/GeneratorProbe/Resources/Views',
        );
    }
}
