<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ACA-YEAR-02 / ACA-F01 test fixture ONLY.
 *
 * Completes the isolated institutions support table so the real
 * InstitutionRepository can hydrate App\Modules\Institution\Domain\Entity\Institution.
 * This migration is not a product migration and must never be deployed to CENICOM.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (! Schema::hasTable('institutions')) {
            throw new RuntimeException(
                'ACA-YEAR-02 fixture rejected: isolated institutions support table does not exist.'
            );
        }

        // This migration is only valid for the earlier minimal fixture (id only).
        // Refuse to mutate any table that may have been created from another source.
        $unexpectedColumns = [
            'code',
            'name',
            'short_code',
            'official_registration_country',
            'official_registration_authority',
            'official_registration_value',
            'status',
            'created_at',
            'updated_at',
        ];

        foreach ($unexpectedColumns as $column) {
            if (Schema::hasColumn('institutions', $column)) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 fixture rejected: institutions.%s already exists; inspect fixture origin before proceeding.',
                    $column,
                ));
            }
        }

        Schema::table('institutions', function (Blueprint $table): void {
            $table->string('code', 20)->unique('institutions_code_unique');
            $table->string('name', 255);
            $table->string('short_code', 15)->unique('institutions_short_code_unique');
            $table->string('official_registration_country', 2)->nullable();
            $table->string('official_registration_authority', 150)->nullable();
            $table->string('official_registration_value', 100)->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        if (! Schema::hasTable('institutions')) {
            return;
        }

        $requiredColumns = [
            'code',
            'name',
            'short_code',
            'official_registration_country',
            'official_registration_authority',
            'official_registration_value',
            'status',
            'created_at',
            'updated_at',
        ];

        foreach ($requiredColumns as $column) {
            if (! Schema::hasColumn('institutions', $column)) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 fixture rollback refused: institutions.%s is missing; inspect schema before rollback.',
                    $column,
                ));
            }
        }

        Schema::table('institutions', function (Blueprint $table): void {
            $table->dropUnique('institutions_code_unique');
            $table->dropUnique('institutions_short_code_unique');
            $table->dropColumn([
                'code',
                'name',
                'short_code',
                'official_registration_country',
                'official_registration_authority',
                'official_registration_value',
                'status',
                'created_at',
                'updated_at',
            ]);
        });
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: APP_ENV must be testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: default connection must be mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: cached configuration exists.');
        }

        $config = (array) config('database.connections.mysql', []);
        $expected = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3308',
            'database' => 'cenicom_aca_year02_isolated_test',
            'username' => 'aca_year02_test',
        ];

        foreach ($expected as $key => $value) {
            $actual = $config[$key] ?? null;
            if ($key === 'port') {
                $actual = $actual === null ? null : (string) $actual;
            }

            if ($actual !== $value) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 fixture rejected: database.connections.mysql.%s must be %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: DB_URL must be empty.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: DB_PASSWORD must be non-empty.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        if (($row->current_database ?? null) !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException('ACA-YEAR-02 fixture rejected: effective database is not authorized.');
        }
    }
};
