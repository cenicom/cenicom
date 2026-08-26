<?php

declare(strict_types=1);

namespace Tests\Fixtures\View\Isolation;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class InstitutionViewDefinition implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'institution',
            __DIR__ . '/Institution',
        );
    }
}
