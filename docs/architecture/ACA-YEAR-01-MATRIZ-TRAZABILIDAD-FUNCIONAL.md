# CENICOM — ACA-YEAR-01: matriz de trazabilidad funcional

**Fecha de actualización:** 2026-10-26
**Estado:** operaciones de ciclo de vida, corrección de versión oficial, períodos y eventos implementadas en el dominio y verificadas en la base aislada. La matriz mantiene explícitas las brechas que aún no tienen evidencia suficiente.
**Entorno autorizado para pruebas:** `cenicom_aca_year02_isolated_test` (`127.0.0.1:3308`), mediante `phpunit.aca-year02.xml`.
**Límite:** no ejecutar migraciones ni pruebas que modifiquen datos contra la base real `cenicom`.

## 1. Evidencia reciente

### Commits de los frentes funcionales cerrados

| Frente | Commit | Alcance |
|---|---|---|
| Corrección de versión de calendario oficial | `90e831f` | Crear una versión sucesora `draft`, conservar intacta la predecesora y auditar valores anteriores/nuevos. |
| Reapertura de año lectivo | `4fd278d` | Permiso específico, transición `closed` → `active`, preservación de `activated_at`, limpieza de `closed_at` y auditoría transaccional. |
| Administración de períodos académicos | `b0e0d75` | Creación y actualización en versión `draft`, validación temporal, unicidad, prevención de solapamientos y auditoría antes/después. |
| Administración de eventos del calendario | `52032d3` | Creación y actualización en versión `draft`, validación temporal, vínculo con período, regla de exámenes, banderas independientes y auditoría antes/después. |

### Última evidencia de pruebas comunicada

Comando ejecutado en el proyecto local:

```bash
/c/xampp1/php/php.exe vendor/bin/phpunit --configuration=phpunit.aca-year02.xml
```

Resultado informado el 2026-10-25: **`OK (144 tests, 649 assertions)`; código de salida 0**.

Pruebas focalizadas informadas:

- `AcademicYearLifecycleTest`: 26 pruebas, 140 aserciones.
- `AcademicPeriodManagementTest`: 5 pruebas, 26 aserciones.
- `AcademicCalendarEventManagementTest`: 8 pruebas, 38 aserciones.
- Corrección de versión oficial verificada previamente dentro del conjunto focalizado de compatibilidad, que pasó con 48 pruebas y 233 aserciones.

Estos resultados acreditan la suite informada en el entorno aislado; no son evidencia de ejecución contra la base real ni de un flujo HTTP/UI.

## 2. Matriz funcional actualizada

| ID | Regla contractual | Evidencia presente | Brecha verificable | Estado |
|---|---|---|---|---|
| ACA-F01 | No puede haber años lectivos con rangos solapados dentro de la misma institución; los intervalos se comparan con fechas oficiales inclusivas. | `AcademicYearCreator`, repositorio académico y pruebas de solapamiento del ciclo de vida; las versiones oficiales suministran las fechas. | La ejecución concurrente real de dos altas simultáneas debe considerarse no demostrada salvo que exista una prueba concurrente específica identificable. | **Implementado; concurrencia no acreditada por la evidencia reciente** |
| ACA-F02 | Transiciones manuales y autorizadas; cierre posterior al fin oficial y con prerrequisitos; reapertura excepcional, autorizada y auditada. | `AcademicYearActivator`, `AcademicYearCloser`, `ReopenAcademicYear`, `CompleteAcademicYearEvent`; permiso `academic_year.reopen`; pruebas de reapertura permitida, rechazo sin permiso, estado incorrecto y rollback ante fallo de auditoría. | No se ha aportado una prueba de concurrencia para dos transiciones simultáneas del mismo año. | **Implementado y probado en el escenario aislado** |
| ACA-F03 | Cancelar solo en `draft`, exigir motivo, auditar y conservar el registro histórico. | `CancelAcademicYear`, repositorio de cancelación, campo físico de motivo/fecha y pruebas de cancelación. | La ausencia de UI/API queda separada de la regla de dominio y debe verificarse como frente de integración. | **Implementado en dominio; flujo HTTP/UI no acreditado** |
| ACA-F04 | Las versiones oficiales validadas/publicadas son inmutables; una corrección crea una nueva versión; las referencias históricas no se alteran. | Migración de inmutabilidad, servicios de validación/publicación/retiro/corrección y commit `90e831f`; la corrección conserva la versión anterior y audita antes/después. | La prueba de inmutabilidad física y las pruebas de la operación son evidencia distinta; ambas existen en la suite comunicada, pero no implican una interfaz de usuario implementada. | **Implementado y probado en el entorno aislado** |
| ACA-F05 | La versión oficial asignada debe ser compatible con país, autoridad, jurisdicción, régimen, región y calendario A/B; la identidad dudosa no se acepta como confirmada. | `OfficialCalendarCompatibilityValidator`, `ConfigureInstitutionAcademicProfile`, `ConfirmOfficialCalendarPublicationIdentity`, `AcademicYearCreator` y pruebas de compatibilidad. | El control de cambio de calendario institucional A↔B cuando ya existen años activos/cerrados se registra por separado en ACA-F09; no se presume resuelto por la validación de compatibilidad al crear un año. | **Validación de compatibilidad implementada; ACA-F09 pendiente de cierre específico** |
| ACA-F06 | Las operaciones sensibles registran actor, acción, sujeto, resultado, instante y metadatos; la operación y su auditoría respetan la atomicidad definida. Correcciones registran antes/después. | Integración con Core Audit en creación/configuración, ciclo de vida, validación/publicación/retiro/corrección, hitos, períodos y eventos. Las pruebas de reapertura y activación cubren rollback ante fallo de auditoría. | No se ha demostrado por una prueba independiente el fallo de auditoría y rollback para absolutamente cada operación. | **Integración implementada; cobertura exhaustiva de fallo de auditoría parcial** |
| ACA-F07 | Una versión puede contener una cantidad variable de períodos; cada período debe estar dentro de la ventana oficial; no hay solapamiento inclusivo; los espacios vacíos se permiten. Los eventos existentes no pueden quedar fuera tras actualizar un período. | `CreateAcademicPeriod`, `UpdateAcademicPeriod`, `AcademicPeriodDataValidator`, repositorio, restricciones/triggers temporales y commit `b0e0d75`; pruebas focalizadas 5/26 y suite total 144/649. | No se ha mostrado una prueba concurrente de dos períodos insertados simultáneamente para la misma versión. | **Implementado y probado; concurrencia no acreditada** |
| ACA-F08 | Eventos dentro de la ventana oficial; si están ligados a un período deben quedar dentro de él y en la misma versión; los intervalos de exámenes requieren período. Obligatoriedad oficial y prerrequisitos de activación/cierre son distintos. | `CreateAcademicCalendarEvent`, `UpdateAcademicCalendarEvent`, `AcademicCalendarEventDataValidator`, repositorio, restricciones/triggers temporales y commit `52032d3`; pruebas focalizadas 8/38 y suite total 144/649. | No se ha mostrado una prueba concurrente de códigos de evento duplicados insertados simultáneamente ni un flujo HTTP/UI. | **Implementado y probado en el entorno aislado; concurrencia e interfaz no acreditadas** |
| ACA-F09 | El cambio institucional A↔B solo se permite si no existen años dependientes `active` o `closed`; los casos permitidos deben seguir permiso y auditoría. | Existe `ConfigureInstitutionAcademicProfile` y el perfil académico institucional almacena el calendario. | Falta evidencia focalizada que pruebe el rechazo del cambio A↔B con años `active`/`closed` y los casos permitidos con solo `draft`/`cancelled`, incluyendo auditoría. | **Pendiente de cierre funcional y pruebas específicas** |

## 3. Brechas restantes y siguiente orden de trabajo

1. **ACA-F09 — cambio institucional A↔B:** inspección puntual del servicio existente y su prueba; implementar únicamente si falta el guard contractual. Después, cubrir combinaciones `active`/`closed` frente a `draft`/`cancelled` y auditoría.
2. **Concurrencia ACA-F01/F07/F08:** no declarar la concurrencia cerrada a partir de bloqueos o índices por sí solos. Añadir pruebas concurrentes solo si forman parte del alcance inmediato acordado.
3. **Integración HTTP/UI:** el inventario de servicios de dominio no acredita por sí mismo controladores, rutas, formularios o navegación. Tratarlo como frente separado y confirmar primero si ya existe antes de desarrollarlo.
4. **Auditoría ante fallo:** la atomicidad está probada para ciertos cambios de ciclo de vida; no inferir que todas las operaciones tienen una prueba de fallo del audit recorder.

No reabrir los contratos D01–D20 ni P01–P14. No crear migraciones para estos puntos sin demostrar una brecha física. No ejecutar pruebas o migraciones contra `cenicom`.

## 4. Límites de Git y evidencia

- Preparar commits por frente con rutas explícitas; no usar `git add .`.
- No incluir copias de migraciones de prueba aislada ni artefactos auxiliares en commits de producto.
- Estado informado antes de esta actualización: árbol de trabajo limpio, `HEAD` en `52032d3` (`feat(academic): manage official calendar events`).
- Esta actualización de matriz requiere su propio diff y commit; no implica alterar lógica de negocio.
