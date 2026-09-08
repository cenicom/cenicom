<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CENICOMPermissionMiddleware
{
    public function __construct(
        private AuthorizationServiceInterface $authorization,
        private IdentityInterface $identity,
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $permission,
    ): Response {
        foreach (explode('|', $permission) as $requiredPermission) {
            $requiredPermission = trim($requiredPermission);

            if (
                $requiredPermission !== ''
                && $this->authorization->can(
                    $this->identity,
                    $requiredPermission,
                )
            ) {
                return $next($request);
            }
        }

        abort(403);
    }
}
