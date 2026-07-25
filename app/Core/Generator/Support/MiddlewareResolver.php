<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use App\Core\Generator\DTO\SecurityDefinition;
use App\Core\Generator\Security\MiddlewareRegistry;

final class MiddlewareResolver
{
    private MiddlewareRegistry $registry;

    public function __construct(
        MiddlewareRegistry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Resuelve middleware Laravel.
     *
     * @return array<int,string>
     */
    public function resolve(
        SecurityDefinition $security
    ): array {

        $middlewares = [];

        if ($security->requiresAuth()) {
            $middlewares[] = 'auth';
        }

        if ($security->requiresVerified()) {
            $middlewares[] = 'verified';
        }

        foreach ($security->middleware() as $middleware) {

            $resolved = $this->registry->resolve(
                $middleware
            );

            if ($resolved !== null) {
                $middlewares[] = $resolved;
            }
        }

        return $middlewares;
    }
}
