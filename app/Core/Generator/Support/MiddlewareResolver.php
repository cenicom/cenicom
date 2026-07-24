<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use App\Core\Generator\DTO\SecurityDefinition;

final class MiddlewareResolver
{
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
            $middlewares[] = $middleware;
        }

        return $middlewares;
    }
}
