<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;
use PHPUnit\Framework\TestCase;

final class PermissionCheckerContractTest extends TestCase
{
    public function test_checker_contract_exists(): void
    {
        $reflection = new \ReflectionClass(
            PermissionCheckerInterface::class
        );

        $this->assertTrue(
            $reflection->isInterface()
        );

        $this->assertTrue(
            $reflection->hasMethod('can')
        );
    }


    public function test_checker_can_be_implemented(): void
    {
        $checker = new FakePermissionChecker();

        $this->assertInstanceOf(
            PermissionCheckerInterface::class,
            $checker
        );
    }
}


/**
 * Fake implementation for contract validation.
 */
final class FakePermissionChecker implements PermissionCheckerInterface
{
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {
        return true;
    }
}
