<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudActionFilterInterface;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class CrudActionFilter implements
    CrudActionFilterInterface
{
    /**
     * @param array<int, CrudAction> $actions
     *
     * @return array<int, CrudAction>
     */
    public function authorized(
        IdentityInterface $identity,
        array $actions,
    ): array {
        return array_values(
            array_filter(
                $actions,
                static fn (CrudAction $action): bool =>
                    $action->authorized($identity),
            ),
        );
    }
}
