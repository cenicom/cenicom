<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use RuntimeException;

final class MigrationFileResolver
{
    public function resolve(
        string $migrationsPath,
        string $table,
    ): ?string {
        $pattern = $migrationsPath
            . DIRECTORY_SEPARATOR
            . '*_create_'
            . $table
            . '_table.php';

        $files = glob($pattern);

        if ($files === false || $files === []) {
            return null;
        }

        if (count($files) > 1) {
            throw new RuntimeException(
                sprintf(
                    'Multiple create migrations found for table [%s].',
                    $table
                )
            );
        }

        return $files[0];
    }
}
