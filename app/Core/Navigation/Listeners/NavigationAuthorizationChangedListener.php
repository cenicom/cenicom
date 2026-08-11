<?php

declare(strict_types=1);

namespace App\Core\Navigation\Listeners;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInvalidatorInterface;
use App\Core\Security\Authorization\Events\AuthorizationChanged;

final readonly class NavigationAuthorizationChangedListener
{
    public function __construct(
        private NavigationCacheInvalidatorInterface $invalidator,
    ) {
    }

    public function handle(
        AuthorizationChanged $event
    ): void {
        if (
            $event->scope === AuthorizationChanged::SCOPE_USER
            && $event->identityId !== null
        ) {
            $this->invalidator->user(
                $event->identityId
            );

            return;
        }

        if (
            $event->scope === AuthorizationChanged::SCOPE_ROLE
        ) {
            $this->invalidator->role(
                $event->role ?? ''
            );
        }
    }
}
