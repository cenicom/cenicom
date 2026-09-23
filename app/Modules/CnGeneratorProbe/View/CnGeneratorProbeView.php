<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class CnGeneratorProbeView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'cn_generator_probes',
            'app/Modules/CnGeneratorProbe/Resources/Views',
        );
    }
}
