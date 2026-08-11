<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class AuthorizationChanged implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public const SCOPE_USER = 'user';

    public const SCOPE_ROLE = 'role';

    public function __construct(
        public string $scope,
        public int|string|null $identityId = null,
        public ?string $role = null,
    ) {
    }

    public static function user(
        int|string $identityId
    ): self {
        return new self(
            scope: self::SCOPE_USER,
            identityId: $identityId,
        );
    }

    public static function role(
        string $role
    ): self {
        return new self(
            scope: self::SCOPE_ROLE,
            role: $role,
        );
    }
}
