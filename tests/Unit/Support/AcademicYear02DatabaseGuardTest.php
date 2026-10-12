<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Support\AcademicYear02DatabaseGuard;

final class AcademicYear02DatabaseGuardTest extends TestCase
{
    public function test_accepts_the_authorized_database_name(): void
    {
        AcademicYear02DatabaseGuard::assertAuthorizedDatabase(
            'cenicom_aca_year02_isolated_test',
        );

        $this->assertTrue(true);
    }

    public function test_rejects_the_alternative_test_database_name(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'ACA-YEAR-02 rechazó la base de datos real: '
            . 'cenicom_aca_year02_test',
        );

        AcademicYear02DatabaseGuard::assertAuthorizedDatabase(
            'cenicom_aca_year02_test',
        );
    }

    public function test_rejects_an_empty_database_name(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'ACA-YEAR-02 rechazó la base de datos real: ',
        );

        AcademicYear02DatabaseGuard::assertAuthorizedDatabase('');
    }
}
