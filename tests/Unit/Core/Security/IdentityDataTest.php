<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use App\Core\Security\DTO\IdentityData;
use PHPUnit\Framework\TestCase;

final class IdentityDataTest extends TestCase
{
    public function test_creates_authenticated_identity(): void
    {
        $identity = new IdentityData(
            id: 1,
            name: 'Administrator',
            roles: [
                'SUPER_ADMIN',
            ],
            permissions: [
                'inventory.products.create',
            ],
            authenticated: true,
        );

        $this->assertSame(1, $identity->id);
        $this->assertSame('Administrator', $identity->name);
        $this->assertTrue($identity->authenticated);

        $this->assertContains(
            'SUPER_ADMIN',
            $identity->roles
        );

        $this->assertContains(
            'inventory.products.create',
            $identity->permissions
        );
    }


    public function test_guest_identity_is_anonymous(): void
    {
        $identity = IdentityData::guest();

        $this->assertNull($identity->id);
        $this->assertSame('Guest', $identity->name);
        $this->assertFalse($identity->authenticated);

        $this->assertEmpty($identity->roles);
        $this->assertEmpty($identity->permissions);
    }


    public function test_checks_permission(): void
    {
        $identity = new IdentityData(
            id: 5,
            name: 'Manager',
            permissions: [
                'treasury.payment.approve',
            ],
            authenticated: true,
        );

        $this->assertTrue(
            $identity->can(
                'treasury.payment.approve'
            )
        );

        $this->assertFalse(
            $identity->can(
                'inventory.products.delete'
            )
        );
    }


    public function test_checks_role(): void
    {
        $identity = new IdentityData(
            id: 10,
            name: 'Director',
            roles: [
                'DIRECTOR',
            ],
        );

        $this->assertTrue(
            $identity->hasRole('DIRECTOR')
        );

        $this->assertFalse(
            $identity->hasRole('ACCOUNTANT')
        );
    }


    public function test_exports_identity_as_array(): void
    {
        $identity = new IdentityData(
            id: 20,
            name: 'Accountant',
            roles: [
                'ACCOUNTANT',
            ],
            permissions: [
                'treasury.payments.view',
            ],
            authenticated: true,
        );

        $data = $identity->toArray();

        $this->assertSame(
            20,
            $data['id']
        );

        $this->assertSame(
            'Accountant',
            $data['name']
        );

        $this->assertTrue(
            $data['authenticated']
        );

        $this->assertContains(
            'ACCOUNTANT',
            $data['roles']
        );
    }
}
