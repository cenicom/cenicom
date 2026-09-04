<?php

declare(strict_types=1);

namespace Tests\Fixtures\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class InstitutionTestViewDefinition implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'institutions',
            dirname(__DIR__, 3) . '/app/Modules/Institution/resources/views',
        );
    }
}
