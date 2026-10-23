# CENICOM — ACA-YEAR-02
## Registro canónico de contratos, implementación aislada y evidencia

**Identificador:** ACA-YEAR-02
**Versión de consolidación:** 1.0
**Fecha de corte de evidencia:** 2026-10-23
**Estado:** frentes ACA-F01 a ACA-F07 verificados en la base aislada; consolidación documental y control Git pendientes de incorporación/commit; diseño y migraciones de producto siguen pendientes.
**Base autorizada exclusivamente para estas pruebas:** `cenicom_aca_year02_isolated_test`, host `127.0.0.1`, puerto `3308`, usuario `aca_year02_test`, configuración PHPUnit `phpunit.aca-year02.xml`.

> **Regla de autoridad.** Las decisiones funcionales D01–D20 y P01–P14 se reportaron formalmente cerradas por el usuario el 2026-10-20. Este registro consolida la síntesis operativa y la evidencia de implementación aislada posterior; no reemplaza ni reinterpreta el texto completo de esos contratos. La decisión temporal ACA-F07-C02 fue confirmada adicionalmente el 2026-10-23.

---

## 1. Resumen ejecutivo

### Verificado en el entorno aislado

- Los frentes funcionales ACA-F01 a ACA-F07 han tenido suites específicas ejecutadas; resultados por frente en `ACA-YEAR-02-REGISTRO-CAMBIOS.md`.
- La migración `100009` terminó con `DONE`; la suite de inmutabilidad reportó `OK (11 tests, 45 assertions)`.
- La migración `100010` terminó con `DONE`; la suite de compatibilidad reportó `OK (11 tests, 43 assertions)`.
- La migración `100011` terminó con `DONE`; la suite de integridad temporal reportó `OK (17 tests, 54 assertions)`.
- Después de ajustar las fixtures para respetar la protección ACA-F04/F07 y delegar la limpieza al rollback transaccional, la batería `tests/Feature/Modules/Academic` reportó `OK (80 tests, 323 assertions)`, código PHPUnit `0`.
- La suite `AcademicYearLifecycleTest.php`, con cobertura de auditoría ampliada, reportó `OK (15 tests, 73 assertions)`.

Estos resultados demuestran el comportamiento cubierto por las pruebas en la base aislada. No demuestran un despliegue de producción, un flujo completo de administración UI/API, ni que exista un commit de Git asociado.

### Pendientes de consolidación

1. Incorporar estos documentos al repositorio y registrar un commit identificable.
2. Confirmar el estado real actual de Git; el último `git status --short` compartido mostraba componentes ACA (carpeta de módulo, migraciones y pruebas) como `??` (sin seguimiento). La situación actual debe verificarse de nuevo.
3. Diseñar y aprobar las verdaderas migraciones de producto, separadas de las migraciones aisladas actuales.
4. Decidir y diseñar la persistencia/administración de los perfiles académicos institucionales para producto; `institution_academic_profiles` de esta fase es tabla de soporte aislado.
5. Diseñar los flujos de administración/importación/consulta para autoridades, publicaciones, versiones, períodos y eventos, pues la evidencia reportada no muestra un CRUD completo de esos catálogos.

---

## 2. Entorno y límites de seguridad

Entorno reportado: Windows 11; proyecto `C:\xampp1\htdocs\cenicom`; Laravel 13.18.1; PHP 8.3.31; MariaDB 10.4.32 en puerto `3308`; host `127.0.0.1`; usuario de pruebas `aca_year02_test`.

Límites obligatorios:

1. No ejecutar estas migraciones contra la base real `cenicom`.
2. No retirar, debilitar ni eludir las guardas de aislamiento.
3. Antes de cualquier comando con efectos sobre la base, comprobar `APP_ENV=testing`, la conexión, el host/puerto, `DB_URL` vacío y `SELECT DATABASE()` con resultado exacto `cenicom_aca_year02_isolated_test`.
4. No ejecutar `migrate:fresh` ni rollback general sobre el proyecto.
5. No tocar `app/Core/Generator` salvo evidencia reproducible de que un defecto pertenece a Generator.
6. No hacer cambios oportunistas en providers, navegación, CRUD, vistas o módulos ajenos.
7. No debilitar los triggers ACA-F04 ni ACA-F07 para conseguir que una prueba pase.
8. No declarar un cambio aprobado/committeado si no se dispone de su diff y SHA verificable.

---

## 3. Contrato funcional consolidado (síntesis operativa)

Las siguientes reglas son una síntesis de las decisiones cerradas; la fuente completa aprobada D01–D20/P01–P14 conserva autoridad.

### Institución, sedes y calendario

- El año lectivo pertenece a una institución.
- Todas las sedes de una institución comparten el calendario A/B y las fechas del año lectivo.
- La selección A/B es una decisión institucional; una configuración global no es fuente de verdad.
- El perfil de compatibilidad comprende país, autoridad/regulador, jurisdicción, régimen, región y calendario A/B, contrastado con los datos oficiales de registro de la institución donde el contrato lo exige.

### Publicaciones oficiales y versiones

- Las publicaciones oficiales tienen autoridad emisora identificada y versiones relacionadas explícitamente.
- La identidad no resuelta no puede utilizarse como si fuese una publicación confirmada.
- La compatibilidad considera país, autoridad/regulador, jurisdicción/régimen/región y calendario A/B.
- Una versión disponible requiere fechas y evidencia de procedencia/validación suficiente; una URL por sí sola no basta como evidencia.
- Las versiones validadas, disponibles o retiradas conservan su contenido histórico; las correcciones se representan mediante una nueva versión, no sobrescribiendo una versión publicada.
- El retiro quita disponibilidad para nuevas asignaciones, sin destruir referencias históricas.

### Año lectivo y ciclo de vida

- Estados ordinarios: `draft`, `active`, `closed`; la cancelación de un borrador conserva el registro y el motivo.
- La activación y el cierre son acciones manuales de identidad autorizada con permisos diferenciados.
- Solo el borrador permite las modificaciones ordinarias de fechas y solo el borrador puede cancelarse; cancelar exige motivo y auditoría.
- El año usa las fechas oficiales de la versión asignada.
- No hay solapamiento inclusivo entre años no cancelados de una misma institución; instituciones distintas pueden usar intervalos iguales.
- El código de ciclo no se reutiliza por cancelar un registro previo.
- El cierre requiere superar la fecha oficial final y completar los prerrequisitos de cierre.
- Las reaperturas y correcciones excepcionales requieren autorización y auditoría; las correcciones deben conservar antes/después.
- El cambio institucional A↔B depende de que no existan años dependientes activos o cerrados, según el contrato aprobado.

### Períodos, eventos y exámenes — ACA-F07

- Una versión oficial puede declarar cualquier cantidad de períodos; no se codifica un número fijo de trimestres/semestres/bimestres.
- **Decisión confirmada el 2026-10-23:** los períodos de una misma versión oficial no se pueden solapar; los huecos entre períodos están permitidos. Los intervalos son inclusivos, por lo que compartir un día es solapamiento.
- Todo período debe estar contenido en la ventana oficial de su versión.
- Todo evento debe estar contenido en la ventana oficial de la versión. Si referencia un período, debe quedar contenido en ese período y en la misma versión.
- Los eventos generales pueden carecer de período; `EXAM_INTERVAL` exige período y debe quedar dentro de ese período.
- `is_officially_required`, `activation_prerequisite` y `closure_prerequisite` son atributos distintos; no se deriva uno automáticamente de otro.
- La validación temporal cubre escrituras `INSERT` y `UPDATE`, incluidos cambios de período que dejarían eventos existentes fuera de sus fechas.

### Auditoría y permisos

- Las entradas de auditoría contienen actor, acción, sujeto, resultado, instante y metadatos.
- Fallos de auditoría que acompañan una operación académica deben revertir la operación dentro de la transacción correspondiente.
- Las autorizaciones diferencian creación/compatibilidad, cancelación, activación, cierre, cumplimiento de hitos y funciones de autoridad/publicación/corrección/retirada de acuerdo con el contrato completo.

---

## 4. Estado por frente y evidencia

| Frente | Alcance | Evidencia de suite específica reportada | Estado |
|---|---|---:|---|
| ACA-F01 | Creación de año lectivo y solapamiento | `OK (11 tests, 34 assertions)` | Aprobado en aislada |
| ACA-F02 | Activación, cierre y prerrequisitos | Inicialmente `OK (14 tests, 56 assertions)`; suite vigente ampliada a 15/73 | Integrado con F06/F07 |
| ACA-F03 | Cancelación, motivo, permiso, auditoría | `OK (9 tests, 48 assertions)` | Aprobado en aislada |
| ACA-F04 | Inmutabilidad de versión oficial | `OK (11 tests, 45 assertions)` | Aprobado en aislada; trigger preservado |
| ACA-F05 | Compatibilidad del perfil institucional | `OK (11 tests, 43 assertions)` | Aprobado en aislada |
| ACA-F06 | Auditoría de operaciones y fallo transaccional | `OK (15 tests, 73 assertions)` en `AcademicYearLifecycleTest.php` tras ampliar cobertura | Aprobado en aislada |
| ACA-F07 | Integridad temporal de períodos, eventos y exámenes | `OK (17 tests, 54 assertions)` | Aprobado en aislada |
| Regresión integral | `tests/Feature/Modules/Academic` | `OK (80 tests, 323 assertions)`, código `0` | Aprobado el 2026-10-23 |

Los conteos por frente no deben sumarse: algunas suites y aserciones se comparten o han sido ampliadas por frentes posteriores. El resultado integral de 80/323 es la medición consolidada de la última ejecución reportada.

---

## 5. Alcance de la implementación que sí está demostrado

- Existe un módulo `app/Modules/Academic` con servicios/repositorios para creación de años, ciclo de vida, cancelación, completitud de hitos y validación de compatibilidad.
- El esquema aislado dispone de tablas para autoridades, publicaciones, versiones, años, períodos, eventos, perfiles académicos de institución y completitud de eventos; hay tablas de soporte específicas de test para instituciones y auditoría.
- La inmutabilidad de versiones oficiales está reforzada por triggers de prueba ACA-F04.
- La integridad temporal período/evento está reforzada por triggers de prueba ACA-F07.
- Las pruebas cubren los frentes reportados y una batería integral de 80/323.

## 6. Lo que estos resultados no demuestran

- No demuestran que las migraciones de `database/migrations/aca_year02/` puedan desplegarse en producción; están estrictamente guardadas para la base de pruebas.
- No demuestran un CRUD completo, interfaces o flujos autorizados para administrar autoridades, publicaciones, versiones, períodos o eventos.
- No cierran el diseño de la persistencia de perfiles académicos institucionales en el esquema real.
- No demuestran concurrencia real multi-conexión en todos los flujos; la suite de solapamiento no debe describirse como prueba de concurrencia real a menos que se ejecute explícitamente.
- No se ha aportado un SHA de commit que identifique este conjunto de cambios.

---

## 7. Próxima secuencia autorizada

1. Incorporar este registro, la matriz de migraciones y el registro de cambios en `docs/architecture/`.
2. Auditar el `git status --short` actual y clasificar cada archivo nuevo/modificado; evitar `git add .`.
3. Revisar el diff por archivos y generar un commit limitado a documentación/contratos ya aprobados.
4. Diseñar una especificación de persistencia de producto independiente, con tablas/columnas/FK/índices/constraints y una matriz campo→regla contractual.
5. Acordar explícitamente cómo se guarda y administra el perfil académico institucional en la base de producto.
6. Solo tras aceptar diseño físico de producto, implementar migraciones bajo la ruta normal `database/migrations/`, sin reutilizar tablas fixture ni guards dirigidos a la base aislada.
7. Mantener `database/migrations/aca_year02/` como zona de prueba; no copiar sus archivos al conjunto de producción.

No se abre un nuevo frente funcional hasta completar el control de cambios de esta consolidación.
