<?php

declare(strict_types=1);

$root = getcwd();
$relativePath = 'tests/Feature/Modules/Academic/AcademicYearLifecycleTest.php';
$path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

if (!is_file($path)) {
    fwrite(STDERR, "ERROR: no se encuentra {$relativePath}. Ejecute el script desde la raíz del proyecto.\n");
    exit(1);
}

$source = file_get_contents($path);
if ($source === false) {
    fwrite(STDERR, "ERROR: no se pudo leer {$relativePath}.\n");
    exit(1);
}

$oldStart = "'start_date' => '2026-01-02',";
$oldEnd = "'end_date' => '2026-01-05',";
$newStart = "'start_date' => '2026-01-12',";
$newEnd = "'end_date' => '2026-01-15',";

$startCount = substr_count($source, $oldStart);
$endCount = substr_count($source, $oldEnd);

if ($startCount !== 1 || $endCount !== 1) {
    fwrite(STDERR, sprintf(
        "SIN CAMBIOS: se esperaba encontrar una sola pareja de fechas de fixture; encontrados start=%d, end=%d. Inspeccione createEvent() manualmente.\n",
        $startCount,
        $endCount,
    ));
    exit(2);
}

$updated = str_replace([$oldStart, $oldEnd], [$newStart, $newEnd], $source);

if ($updated === $source || file_put_contents($path, $updated) === false) {
    fwrite(STDERR, "ERROR: no se pudo aplicar la corrección de la fixture.\n");
    exit(1);
}

echo "ACTUALIZADO: {$path}\n";
echo "La fixture de eventos usa ahora 2026-01-12 a 2026-01-15, dentro del intervalo oficial que comienza 2026-01-10.\n";
echo "Solo se modificó AcademicYearLifecycleTest.php; no se tocaron la migración 100011 ni triggers.\n";
