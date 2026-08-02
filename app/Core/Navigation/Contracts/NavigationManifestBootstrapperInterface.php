<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

interface NavigationManifestBootstrapperInterface
{
    public function boot(): void;
}
