<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Contracts\IdentityInterface;

interface NavigationServiceInterface
{
    /**
     * Obtiene el árbol de navegación filtrado
     * según la identidad actual.
     */
    public function tree(
        IdentityInterface $identity
    ): NavigationTreeData;
}
