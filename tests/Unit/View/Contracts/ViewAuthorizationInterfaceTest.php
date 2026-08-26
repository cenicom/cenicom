<?php

declare(strict_types=1);

namespace Tests\Unit\View\Contracts;

use App\Core\Security\Contracts\IdentityInterface;
use App\View\Contracts\ViewAuthorizationInterface;
use PHPUnit\Framework\TestCase;

final class ViewAuthorizationInterfaceTest extends TestCase
{
    public function test_contract_can_resolve_permission_for_identity(): void
    {
        $authorization = new class implements ViewAuthorizationInterface {
            public function can(
                IdentityInterface $identity,
                string $permission,
            ): bool {
                return true;
            }
        };

        $identity = $this->createMock(
            IdentityInterface::class
        );

        self::assertTrue(
            $authorization->can(
                $identity,
                'institution.create',
            ),
        );
    }
}
