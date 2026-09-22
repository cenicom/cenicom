<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support;

//use App\Core\Generator\Support\MigrationFileResolver;
use App\Core\Generator\Support\MigrationFileResolver;
use RuntimeException;
use Tests\Support\GeneratorTestCase;

final class MigrationFileResolverTest extends GeneratorTestCase
{
    public function test_returns_null_when_create_migration_does_not_exist(): void
    {
        $resolver = new MigrationFileResolver();

        $result = $resolver->resolve(
            $this->migrationsPath(),
            'currencies'
        );

        $this->assertNull($result);
    }

    public function test_returns_existing_create_migration_when_exactly_one_exists(): void
    {
        $resolver = new MigrationFileResolver();

        $this->createMigrationDirectory();

        $migration = $this->migrationsPath()
            . DIRECTORY_SEPARATOR
            . '2020_01_01_000000_create_currencies_table.php';

        file_put_contents(
            $migration,
            '<?php'
        );

        $result = $resolver->resolve(
            $this->migrationsPath(),
            'currencies'
        );

        $this->assertSame(
            $migration,
            $result
        );
    }

    public function test_ignores_non_create_migrations_for_same_table(): void
    {
        $resolver = new MigrationFileResolver();

        $this->createMigrationDirectory();

        $createMigration = $this->migrationsPath()
            . DIRECTORY_SEPARATOR
            . '2020_01_01_000000_create_currencies_table.php';

        $otherMigration = $this->migrationsPath()
            . DIRECTORY_SEPARATOR
            . '2020_01_02_000000_add_symbol_to_currencies_table.php';

        file_put_contents(
            $createMigration,
            '<?php'
        );

        file_put_contents(
            $otherMigration,
            '<?php'
        );

        $result = $resolver->resolve(
            $this->migrationsPath(),
            'currencies'
        );

        $this->assertSame(
            $createMigration,
            $result
        );
    }

    public function test_rejects_multiple_existing_create_migrations(): void
    {
        $resolver = new MigrationFileResolver();

        $this->createMigrationDirectory();

        file_put_contents(
            $this->migrationsPath()
                . DIRECTORY_SEPARATOR
                . '2020_01_01_000000_create_currencies_table.php',
            '<?php'
        );

        file_put_contents(
            $this->migrationsPath()
                . DIRECTORY_SEPARATOR
                . '2020_01_02_000000_create_currencies_table.php',
            '<?php'
        );

        $this->expectException(RuntimeException::class);

        $resolver->resolve(
            $this->migrationsPath(),
            'currencies'
        );
    }

    private function createMigrationDirectory(): void
    {
        mkdir(
            $this->migrationsPath(),
            0777,
            true
        );
    }
}
