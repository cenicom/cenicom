<?php

declare(strict_types=1);

namespace Tests\Fixtures\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class TestViewDefinition implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'tests',
            __DIR__.'/views',
        );
    }
}
