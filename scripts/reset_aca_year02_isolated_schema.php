<?php

declare(strict_types=1);

/**
 * Controlled recovery utility for the disposable ACA-YEAR-02 isolated database.
 * It never connects to `cenicom`, and refuses to drop any ACA table with rows.
 * Run only after reviewing docs/RECUPERACION-ESTADO-AISLADO.md.
 */

const EXPECTED_DATABASE = 'cenicom_aca_year02_isolated_test';
const EXPECTED_HOST = '127.0.0.1';
const EXPECTED_PORT = '3308';
const CONFIRMATION_TOKEN = 'RESET_CENICOM_ACA_YEAR02_ISOLATED_TEST';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "ERROR: este script solo puede ejecutarse por CLI.\n");
    exit(1);
}

if ((getenv('ACA_YEAR02_RESET_CONFIRM') ?: '') !== CONFIRMATION_TOKEN) {
    fwrite(STDERR, "ERROR: falta ACA_YEAR02_RESET_CONFIRM con el token exacto documentado.\n");
    exit(1);
}

$adminUser = getenv('ACA_YEAR02_ADMIN_USERNAME') ?: 'root';
$adminPassword = getenv('ACA_YEAR02_ADMIN_PASSWORD');

if ($adminUser !== 'root') {
    fwrite(STDERR, "ERROR: la recuperación requiere la cuenta administrativa local root.\n");
    exit(1);
}

if ($adminPassword === false) {
    fwrite(STDERR, "ERROR: define ACA_YEAR02_ADMIN_PASSWORD localmente; puede estar vacía si root no tiene contraseña.\n");
    exit(1);
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    EXPECTED_HOST,
    EXPECTED_PORT,
    EXPECTED_DATABASE,
);

$managedTables = [
    'academic_calendar_events',
    'academic_periods',
    'academic_years',
    'official_calendar_publication_versions',
    'official_calendar_publications',
    'academic_regulatory_authorities',
];

$migrationNames = [
    '2026_10_22_100000_create_institutions_isolated_test_support_table',
    '2026_10_22_100001_create_academic_regulatory_authorities_and_official_calendar_publications_tables',
    '2026_10_22_100002_create_official_calendar_publication_versions_table',
    '2026_10_22_100003_create_academic_years_table',
    '2026_10_22_100004_create_academic_periods_table',
    '2026_10_22_100005_create_academic_calendar_events_table',
];

try {
    $pdo = new PDO($dsn, $adminUser, $adminPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $actualDatabase = $pdo->query('SELECT DATABASE()')->fetchColumn();
    if ($actualDatabase !== EXPECTED_DATABASE) {
        throw new RuntimeException('Base efectiva no coincide con la base aislada autorizada.');
    }

    $tableQuery = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $tableExists = static function (string $table) use ($tableQuery): bool {
        $tableQuery->execute([$table]);
        return (int) $tableQuery->fetchColumn() > 0;
    };

    if (!$tableExists('migrations')) {
        throw new RuntimeException('No existe la tabla migrations; recuperación detenida sin cambios.');
    }

    // institutions is managed only if it is the empty one-column test fixture.
    if ($tableExists('institutions')) {
        $columnsStmt = $pdo->query(
            "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY, CHARACTER_SET_NAME, COLLATION_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'institutions'
             ORDER BY ORDINAL_POSITION"
        );
        $columns = $columnsStmt->fetchAll();
        if (count($columns) !== 1
            || $columns[0]['COLUMN_NAME'] !== 'id'
            || strtolower((string) $columns[0]['COLUMN_TYPE']) !== 'char(26)'
            || $columns[0]['COLUMN_KEY'] !== 'PRI'
            || $columns[0]['CHARACTER_SET_NAME'] !== 'utf8mb4'
            || $columns[0]['COLLATION_NAME'] !== 'utf8mb4_unicode_ci') {
            throw new RuntimeException('institutions existe, pero no coincide exactamente con el fixture mínimo; no se eliminará.');
        }
        $count = (int) $pdo->query('SELECT COUNT(*) FROM `institutions`')->fetchColumn();
        if ($count !== 0) {
            throw new RuntimeException('institutions fixture contiene filas; recuperación detenida para no perder datos.');
        }
    }

    // Full preflight before any DDL: all managed product/test tables must be empty.
    $existingTables = [];
    foreach ($managedTables as $table) {
        if (!$tableExists($table)) {
            continue;
        }
        $count = (int) $pdo->query(sprintf('SELECT COUNT(*) FROM `%s`', $table))->fetchColumn();
        echo sprintf("PREVIO: %s existe; filas=%d\n", $table, $count);
        if ($count !== 0) {
            throw new RuntimeException(sprintf('%s contiene filas; no se eliminará.', $table));
        }
        $existingTables[] = $table;
    }

    echo "BASE EFECTIVA: {$actualDatabase}\n";
    echo "Preflight OK. Solo se eliminarán tablas ACA-YEAR-02 vacías y sus registros de migración.\n";

    // Drop dependent tables first.
    foreach ($managedTables as $table) {
        if (in_array($table, $existingTables, true)) {
            $pdo->exec(sprintf('DROP TABLE `%s`', $table));
            echo "ELIMINADA: {$table}\n";
        }
    }

    if ($tableExists('institutions')) {
        $pdo->exec('DROP TABLE `institutions`');
        echo "ELIMINADA: institutions (fixture aislado vacío)\n";
    }

    $placeholders = implode(',', array_fill(0, count($migrationNames), '?'));
    $delete = $pdo->prepare("DELETE FROM `migrations` WHERE `migration` IN ({$placeholders})");
    $delete->execute($migrationNames);
    echo "REGISTROS DE MIGRACION ELIMINADOS: {$delete->rowCount()}\n";
    echo "RECUPERACION COMPLETADA. Ejecute después las migraciones solo con phpunit.aca-year02.xml/.env configurados para testing.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n");
    exit(1);
}
