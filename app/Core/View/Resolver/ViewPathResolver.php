<?php

declare(strict_types=1);

namespace App\Core\View\Resolver;

use App\Core\View\Contracts\ViewPathResolverInterface;
use Illuminate\Contracts\Foundation\Application;

final readonly class ViewPathResolver implements ViewPathResolverInterface
{
    public function __construct(
        private Application $application,
    ) {
    }

    public function resolve(string $path): string
    {
        return $this->application->basePath($path);
    }
}
