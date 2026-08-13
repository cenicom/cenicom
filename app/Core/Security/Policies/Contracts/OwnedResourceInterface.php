<?php

declare(strict_types=1);

namespace App\Core\Security\Policies\Contracts;

interface OwnedResourceInterface
{
    /**
     * Obtiene el identificador de la identidad propietaria.
     */
    public function ownerId(): int|string|null;
}
