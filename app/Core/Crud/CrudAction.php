<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class CrudAction
{
    public function __construct(
        private string $resource,
        private CrudOperation $operation,
        private CrudActionAuthorizationInterface $authorization,
    ) {}

    public function resource(): string
    {
        return $this->resource;
    }

    public function operation(): CrudOperation
    {
        return $this->operation;
    }

    public function authorized(
        IdentityInterface $identity
    ): bool {
        return $this->authorization->allows(
            $identity,
            $this->resource,
            $this->operation,
        );
    }


}
