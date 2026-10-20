<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Tests\Support\AcademicYear02TestCase;

final class AcademicYear02IsolationTest extends AcademicYear02TestCase
{
    public function test_uses_only_the_authorized_database(): void
    {
        $database = $this->app
            ->make('db')
            ->connection('mysql')
            ->selectOne('SELECT DATABASE() AS database_name');

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            $database->database_name,
        );
    }
}
