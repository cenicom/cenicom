<?php

declare(strict_types=1);

namespace App\Core\Generator\Security;

final class MiddlewareRegistry
{
    /**
     * Middleware registrados del ERP.
     *
     * @var array<string,string>
     */
    private array $middlewares = [

        'auth' => 'auth',

        'verified' => 'verified',

        'admin' => 'role:admin',

        'tenant' => 'tenant',

    ];


    /**
     * Resuelve un middleware.
     */
    public function resolve(string $key): ?string
    {
        return $this->middlewares[$key] ?? null;
    }
}
