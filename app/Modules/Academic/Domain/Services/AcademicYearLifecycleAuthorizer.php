<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Auth\Access\AuthorizationException;

final readonly class AcademicYearLifecycleAuthorizer
{
    public function __construct(
        private AuthorizationServiceInterface $authorization,
        private IdentityInterface $identity,
    ) {
    }

    public function assertCan(string $permission): void
    {
        if (! $this->identity->authenticated() || ! $this->authorization->can($this->identity, $permission)) {
            throw new AuthorizationException('No está autorizado para ejecutar esta acción académica.');
        }
    }

    public function actorId(): string
    {
        $id = $this->identity->id();

        if ($id === null || trim((string) $id) === '') {
            throw new AuthorizationException('La identidad autenticada no tiene un identificador auditable.');
        }

        return (string) $id;
    }

    public function actorName(): string
    {
        return $this->identity->name();
    }
}
