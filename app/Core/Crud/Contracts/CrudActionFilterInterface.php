<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\CrudAction;
use App\Core\Security\Contracts\IdentityInterface;

interface CrudActionFilterInterface
{
    /**
     * @param array<int, CrudAction> $actions
     *
     * @return array<int, CrudAction>
     */
    public function authorized(
        IdentityInterface $identity,
        array $actions,
    ): array;
}
