# ACA-YEAR-02 — Matriz de migraciones y destino permitido

**Fecha de corte:** 2026-10-23
**Conclusión de control:** todos los archivos de migración de `database/migrations/aca_year02/` usados en este frente son de esquema aislado/prototipo o soporte de pruebas. No son una lista de migraciones aprobadas para despliegue de producto.

## Clasificación

| ID | Archivo | Función | Clasificación/destino |
|---:|---|---|---|
| 100000 | `2026_10_22_100000_create_institutions_isolated_test_support_table.php` | Crea una tabla `institutions` mínima solo para FKs de prueba. | **Fixture exclusiva de pruebas. No desplegar.** |
| 100001 | `2026_10_22_100001_create_academic_regulatory_authorities_and_official_calendar_publications_tables.php` | Tablas de autoridades reguladoras y publicaciones oficiales. | Prototipo de esquema aislado, con guarda de entorno/base. No desplegar como migration de producto. |
| 100002 | `2026_10_22_100002_create_official_calendar_publication_versions_table.php` | Versiones de publicaciones oficiales, estados, fechas, procedencia y sucesión. | Prototipo de esquema aislado, con guarda estricta. No desplegar como migration de producto. |
| 100003 | `2026_10_22_100003_create_academic_years_table.php` | Tabla de años lectivos y relaciones/restricciones del esquema aislado. | Prototipo de esquema aislado. No desplegar como migration de producto. |
| 100004 | `2026_10_22_100004_create_academic_periods_table.php` | Tabla de períodos académicos variables vinculados a versión. | Prototipo de esquema aislado, con guarda estricta. No desplegar como migration de producto. |
| 100005 | `2026_10_22_100005_create_academic_calendar_events_table.php` | Tabla de eventos oficiales/hitos y tipo `EXAM_INTERVAL`. | Prototipo de esquema aislado, con guarda estricta. No desplegar como migration de producto. |
| 100006 | `2026_10_22_100006_expand_institutions_isolated_test_support_table.php` | Expande la tabla fixture mínima para que el repositorio real de Institution pueda hidratarse en pruebas. | **Fixture exclusiva de pruebas. No desplegar.** |
| 100007 | `2026_10_22_100007_create_audit_entries_isolated_test_support_table.php` | Crea soporte `audit_entries` para pruebas/servicios aislados. | **Soporte exclusivo de pruebas. No desplegar ni usar para sustituir el esquema de auditoría de producto.** |
| 100008 | `2026_10_22_100008_create_academic_year_event_completions_isolated_test_support_table.php` | Persiste completitud de hitos para los tests de ciclo de vida. | **Soporte exclusivo de pruebas. No desplegar.** |
| 100009 | `2026_10_22_100009_enforce_official_calendar_publication_version_immutability.php` | Triggers `trg_ocpv_guard_immutable_update` y `trg_ocpv_guard_immutable_delete`. | **Triggers de prueba con guarda exacta de base aislada. No desplegar.** Las reglas de producto se deben reimplementar tras diseño y revisión de compatibilidad. |
| 100010 | `2026_10_22_100010_create_institution_academic_profiles_isolated_test_support_table.php` | Perfil académico usado para probar compatibilidad de institución/calendario. | **Soporte exclusivo de pruebas. No determina la tabla de producto.** |
| 100011 | `2026_10_23_100011_enforce_academic_period_event_temporal_integrity.php` | Cuatro triggers para rango oficial, no-solapamiento de períodos y contención período/evento/exámenes. | **Triggers de prueba con guarda exacta de base aislada. No desplegar.** Las reglas de producto deben trasladarse en una migración de producto aprobada, después de diseño. |

## Evidencia de aislamiento

- Las migraciones inspeccionadas llaman `assertIsolatedDatabase()` y verifican `APP_ENV=testing`, conexión MySQL, host `127.0.0.1`, puerto `3308`, base `cenicom_aca_year02_isolated_test`, usuario `aca_year02_test`, `DB_URL` vacío, contraseña definida/no vacía, ausencia de configuración cacheada y resultado efectivo de `SELECT DATABASE()`.
- Las migraciones `100000` y `100006` se identifican expresamente como fixture/test support; los títulos de `100007`, `100008` y `100010` también las identifican como tablas de soporte aislado. La migración `100009` declara expresamente que sus triggers pertenecen exclusivamente a la migración aislada. `100011` aplica la misma guarda de base.
- Los tests migratorios `100001–100005` crean entidades que son parte del dominio conceptual, pero el hecho de estar en `database/migrations/aca_year02/` y guardarse para una base aislada impide tratarlos como migraciones de despliegue. Deben usarse como referencia del prototipo, no copiarse al esquema real sin una migración nueva aprobada.

## Reglas de manejo

1. No copiar estos archivos al directorio ordinario `database/migrations/`.
2. No ejecutar `php artisan migrate` general para este frente; durante pruebas se usa la ruta explícita y se verifica la base efectiva.
3. No usar `migrate:fresh`, ni hacer rollbacks destructivos para “limpiar” suites.
4. No eliminar ni desactivar triggers manualmente; no deben debilitarse para arreglar fixtures. Las pruebas deben usar transacciones/rollback y datos que respeten los contratos.
5. Si se requiere producto, crear una **nueva especificación física** y archivos nuevos bajo `database/migrations/` después de aprobar el contrato físico. Reusar conceptos de esquema no equivale a reutilizar estos archivos de test.
6. En el diseño de producto, resolver explícitamente relaciones con las tablas reales `institutions` y auditoría, sin introducir las tablas mínimas de fixture `institutions`/`audit_entries` como sustitutos.

## Puerta de aprobación para migraciones de producto

Antes de escribir una migration de producto debe existir:

- Nombre y orden de dependencia aprobados.
- Tabla/campos/tipos/nulabilidad/índices/FK/CHECK definidos.
- Regla contractual trazada por campo y constraint.
- Estrategia de unicidad/concurrencia y reversión revisada.
- Decisión documentada sobre el perfil académico institucional.
- Pruebas ejecutables, plan de rollout/rollback y revisión de impacto en datos existentes.
- Lista cerrada de archivos/operaciones autorizadas; aprobación explícita antes de migrar.
