<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

final class AcademicYear02DatabaseGuard
{
    public const EXPECTED_DATABASE =
        'cenicom_aca_year02_isolated_test';

    public static function assertAuthorizedDatabase(
        string $actualDatabase,
    ): void {
        if ($actualDatabase !== self::EXPECTED_DATABASE) {
            throw new RuntimeException(
                'ACA-YEAR-02 rechazó la base de datos real: '
                . $actualDatabase,
            );
        }
    }
}
