# ACA-YEAR-02 — Registro de cambios y evidencia

**Fecha de consolidación:** 2026-10-23
**Objetivo:** mantener trazabilidad entre contrato, modificación, comandos, resultado y commit.
**Estado:** los resultados de pruebas se han reportado aprobados en el entorno aislado. No hay SHA de commit verificado en el contexto, por lo que el control Git está pendiente.

## 1. Evidencia de ejecución por frente

| Frente | Archivos/ámbito del cambio reportado | Comando/suite reportada | Resultado reportado |
|---|---|---|---|
| ACA-F01 | Creación de año y prevención de solapamiento; soporte/repositorio de prueba | `AcademicYearCreationOverlapTest.php` | `OK (11 tests, 34 assertions)` |
| ACA-F02 | Activación/cierre y completitud de prerrequisitos; migración aislada `100008` para completitud | `AcademicYearLifecycleTest.php` (corte inicial) | `OK (14 tests, 56 assertions)` en el checkpoint inicial; el conteo se amplió después |
| ACA-F03 | Cancelación, razón obligatoria, permisos y auditoría; registro de `academic_year.cancel` | `AcademicYearCancellationTest.php` | `OK (9 tests, 48 assertions)` tras corregir el registro de permiso |
| ACA-F04 | Inmutabilidad de contenido de versiones validadas/disponibles/retiradas; migración aislada `100009` | `OfficialCalendarPublicationVersionImmutabilityTest.php` | `OK (11 tests, 45 assertions)` |
| ACA-F05 | Validación de perfil institucional y compatibilidad de publicación; soporte aislado `100010` | `AcademicYearCompatibilityTest.php` | `OK (11 tests, 43 assertions)` |
| ACA-F06 | Aserciones de auditoría de hito y rollback ante fallo; solo se amplió `AcademicYearLifecycleTest.php` | `AcademicYearLifecycleTest.php` | `OK (15 tests, 73 assertions)` |
| ACA-F07 | Reglas temporales de período, evento y exámenes; migración aislada `100011` | `AcademicCalendarStructureIntegrityTest.php` | `OK (17 tests, 54 assertions)` |
| Regresión consolidada | Suite completa de pruebas de Academic | `vendor/bin/phpunit --configuration=phpunit.aca-year02.xml tests/Feature/Modules/Academic` | `OK (80 tests, 323 assertions)`, código de salida `0` |

Los conteos por frente no son aditivos porque las suites se solapan/amplían. La referencia de regresión global es 80/323 en la última ejecución reportada después de reparar las fixtures F07.

## 2. Migraciones aplicadas solo en base aislada

| Migración | Evidencia reportada | Destino autorizado |
|---|---|---|
| `100009` inmutabilidad de versiones | `DONE`; tests 11/45 | Solo `cenicom_aca_year02_isolated_test` |
| `100010` perfil académico institucional de prueba | `DONE`; tests F05 11/43 | Solo `cenicom_aca_year02_isolated_test` |
| `100011` integridad temporal período/evento/examen | `DONE`; tests F07 17/54 | Solo `cenicom_aca_year02_isolated_test` |

También existen las migraciones aisladas `100000–100008`; su lista y clasificación completa está en `ACA-YEAR-02-MATRIZ-MIGRACIONES.md`. Ninguna se declara migración de producto.

## 3. Incidencias cerradas con evidencia

- **F03:** la prueba de registro de `academic_year.cancel` pasó después de asegurar que la definición Academic se cargara en el manifiesto. Suite de cancelación luego pasó 9/48.
- **F04:** el trigger de eliminación impidió la limpieza manual de versiones validadas, como estaba diseñado. Se cambiaron las suites de cancelación/creación/ciclo de vida a `DatabaseTransactions`, delegando limpieza al rollback. No se desactivó el trigger.
- **F05:** fixture reparada para usar `ACAFIVETEST` como `short_code`; se corrigió la construcción `OfficialCalendarWindow` para pasar el perfil institucional de compatibilidad con ocho campos. Suite de F05 pasó 11/43.
- **F06:** se ampliaron las aserciones del hito y la prueba de rollback por fallo del auditor en `AcademicYearLifecycleTest.php`; la suite pasó 15/73.
- **F07:** una fixture de `AcademicYearLifecycleTest.php` generaba evento `2026-01-02`–`2026-01-05` fuera del calendario oficial que comenzaba `2026-01-10`. Se ajustó a `2026-01-12`–`2026-01-15`; la suite de ciclo de vida pasó 15/73 y la regresión completa 80/323.

## 4. Control de cambios pendiente

No se proporciona un SHA de commit. El último `git status --short` que se compartió mostraba como `??` (sin seguimiento), entre otros, `app/Modules/Academic/`, `database/migrations/aca_year02/` y varias pruebas Academic. Ese resultado es anterior a los últimos cambios de F06/F07; ejecutar de nuevo `git status --short` para conocer el estado actual.

### Procedimiento de control

1. `git status --short` y clasificar cada archivo nuevo/modificado; no asumir que los archivos `??` están cubiertos por `git diff`.
2. Revisar por separado el contenido de documentación, aplicación, pruebas y migraciones aisladas.
3. No ejecutar `git add .` ni incluir documentos/cambios que no pertenezcan al alcance aprobado.
4. Agregar explícitamente los archivos aceptados; revisar `git diff --cached --stat` y `git diff --cached --name-status`.
5. Ejecutar `git diff --cached --check`.
6. Conservar en el registro los comandos/tests ejecutados y sus resultados.
7. Crear commit dedicado con mensaje que describa el alcance y agregar aquí su SHA real.
8. La documentación del producto no se da por consolidada hasta que el commit exista y sea verificable.

## 5. Plantilla obligatoria por cambio futuro

```text
ID DE CAMBIO / FRENTE:
OBJETIVO ÚNICO:
CONTRATO APLICABLE:
DECISIÓN AUTORIZADA:
ARCHIVOS PERMITIDOS:
ARCHIVOS MODIFICADOS:
MIGRACIÓN / DDL (si aplica):
COMANDO EXACTO:
BASE EFECTIVA:
RESULTADO:
TESTS / ASSERTIONS:
INCIDENCIAS Y RESOLUCIÓN:
PRUEBA DE NO REGRESIÓN:
GIT STATUS:
COMMIT SHA:
PENDIENTES / RIESGOS:
SIGUIENTE PASO AUTORIZADO:
```

## 6. Puerta de salida de esta consolidación

No abrir otro frente funcional hasta que:

- [ ] los tres documentos se hayan incorporado al repositorio;
- [ ] el alcance Git actual se haya revisado y clasificado;
- [ ] se haya acordado una política de archivos/trabajo aislado frente a migraciones de producto;
- [ ] se haya creado un commit documental identificable;
- [ ] quede programada como trabajo posterior la especificación física de producto y la decisión sobre el perfil académico institucional.
