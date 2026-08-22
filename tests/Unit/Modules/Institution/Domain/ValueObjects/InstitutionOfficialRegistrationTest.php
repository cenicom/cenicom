<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Domain\ValueObjects;

use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;
use Tests\TestCase;

final class InstitutionOfficialRegistrationTest extends TestCase
{
    public function test_creates_registration_with_valid_data(): void
    {
        $registration = new InstitutionOfficialRegistration(
            country: 'CO',
            authority: 'Education Authority',
            value: '123456789',
        );

        $this->assertSame('CO', $registration->country);
        $this->assertSame('Education Authority', $registration->authority);
        $this->assertSame('123456789', $registration->value);
    }

    public function test_rejects_empty_country(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionOfficialRegistration(
            country: '',
            authority: 'Education Authority',
            value: '123456789',
        );
    }

    public function test_rejects_empty_authority(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionOfficialRegistration(
            country: 'CO',
            authority: '',
            value: '123456789',
        );
    }

    public function test_rejects_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InstitutionOfficialRegistration(
            country: 'CO',
            authority: 'Education Authority',
            value: '',
        );
    }
}
