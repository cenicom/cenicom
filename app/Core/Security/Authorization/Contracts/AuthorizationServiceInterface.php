<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface AuthorizationServiceInterface
{
    /**
     * Determina si la identidad posee el permiso indicado.
     */
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool;

     /**
     * Determina si la identidad está autorizada
     * mediante la Policy indicada para operar
     * sobre el recurso.
     */
    public function allows(
        IdentityInterface $identity,
        string $policy,
        mixed $resource
    ): bool;


}
