# HT-02-B01 — Matriz consolidada de trazabilidad y cierre documental

**Estado:** Diseño físico aprobado. Matriz de impacto consolidada para cierre documental. Implementación y migraciones no autorizadas.

## 1. Alcance cerrado

La entrega comprende el diseño de las cinco tablas del catálogo de publicaciones oficiales, sus reglas de identidad, versionado, procedencia, períodos, hitos, integridad, autorización, auditoría y concurrencia.

Tablas aprobadas:

1. `academic_issuing_authorities`
2. `academic_official_publications`
3. `academic_official_publication_versions`
4. `academic_official_publication_periods`
5. `academic_official_publication_milestones`

Se excluyen de esta entrega la administración de años académicos, instituciones y sedes; la interfaz de usuario; navegación y CRUD; integración automática con organismos reguladores; y modificaciones al Generator, tablas geográficas importadas o proveedores globales sin evidencia de una necesidad real.

## 2. Trazabilidad de los criterios contractuales

| Criterio | Decisión y restricción | Archivos candidatos | Aceptación |
|---|---|---|---|
| CT-B01-01 | ULID `CHAR(26)`, claves foráneas compatibles, historial sin borrado en cascada. | Nuevas migraciones y modelos del catálogo Academic. | PA-01, PA-03; integridad referencial. |
| CT-B01-02 | Autoridad con identidad estable; no fusionar por nombre; impedir borrado si tiene publicaciones. | Modelo, repositorio y servicio de autoridades. | PA-01, PA-02. |
| CT-B01-03 | Identidad compuesta por país, autoridad, tipo y valor oficial, ámbito y valor de referencia. Identidad incompleta o ambigua no queda disponible. | Value object de identidad, validación y repositorio de publicaciones. | PA-03, PA-05, PA-17. |
| CT-B01-04 | Clave SHA-256 sobre JSON canónico versionado; índice único para claves no nulas; transición de versión controlada. | Servicio de canonicalización, repositorio, migración e índice único. | PA-04, PA-12, PA-24; CAN-01 a CAN-12. |
| CT-B01-05 | Versiones internas consecutivas por publicación; estados `draft`, `validated`, `available`; contenido validado inmutable. | Modelo y servicio de versiones; repositorio con bloqueo transaccional. | PA-06, PA-07, PA-10, PA-11, PA-16. |
| CT-B01-06 | Procedencia documentada; validación exige evidencia suficiente, actor y fecha. No truncar fuentes. | Servicio de validación, reglas de procedencia y persistencia. | PA-08, PA-09. |
| CT-B01-07 | Número variable de períodos; posición desde 1; fechas coherentes; no imponer cuatro períodos ni prohibir solapamientos por defecto. | Modelo, validación y migración de períodos. | PA-19. |
| CT-B01-08 | Hitos `point` o `interval`; código opcional; nombre oficial preservado; obligatoriedad explícita; nombres y códigos repetidos permitidos. | Modelo, validación y migración de hitos. | PA-20, PA-21, PA-22, PA-23. |
| CT-B01-09 | Conservar los datos necesarios para evaluar compatibilidad futura sin inferencias automáticas. No implementar todavía el motor de compatibilidad. | Contrato de dominio y campos ya aprobados del catálogo. | PA-18. |
| CT-B01-10 | Conservar versiones e historial; no incorporar ahora un flujo completo de retiro. | Modelo de versiones y reglas de inmutabilidad. | PA-16; el retiro completo queda fuera de alcance. |
| CT-B01-11 | Autorizar cada operación protegida en el servidor; ocultar botones no sustituye autorización. | Definiciones de permisos y futuros servicios/casos de uso Academic. | PA-15. |
| CT-B01-12 | Dominio y auditoría deben ser atómicos: si falla la auditoría, se revierte la operación de dominio. | Caso de uso transaccional Academic, `AuditRecorderInterface` y repositorio de auditoría existentes. | PA-13, PA-14. |
| CT-B01-13 | La auditoría debe permitir verificar actor, acción, sujeto, resultado y momento. | Integración con `App\Core\Audit`; pruebas de integración específicas. | PA-09, PA-13, PA-14. |
| CT-B01-14 | Reforzar en base de datos las restricciones persistentes que sean representables; complementar con validación de dominio. | Migraciones, índices, claves foráneas y servicios de validación. | PA-04, PA-19, PA-21, PA-22. |
| CT-B01-15 | Proteger concurrencia en secuencias de versión y unicidad de identidad; la base de datos constituye la última barrera de unicidad. | Repositorios y servicios transaccionales; índice único de `identity_key`. | PA-12, PA-24. |

**Nota sobre archivos:** los archivos citados son candidatos de impacto, no confirmación de que las clases Academic ya existan. No se modifica ningún proveedor o módulo existente por mera posibilidad.

## 3. Restricciones físicas transversales

| Restricción aprobada | Aplicación | Prueba de aceptación |
|---|---|---|
| MariaDB 10.4 / InnoDB | Cinco tablas nuevas; claves e índices compatibles. | Migración y pruebas de integridad en la base aislada. |
| Identificadores ULID | `CHAR(26)` para entidades y relaciones internas. | Creación, formato y relaciones. |
| Fechas y marcas temporales | Fechas de negocio `DATE`; timestamps `DATETIME`; política UTC explícita para instantes. | Persistencia y lectura coherentes. |
| Claves foráneas históricas | Sin `ON DELETE CASCADE`; referencias históricas protegidas. | Rechazo de borrados que rompan el historial. |
| Catálogos geográficos importados | No modificar tablas importadas ni crear FK hacia `countries.iso2` sin evidencia de índice único adecuado. | Validación de país contra catálogo existente. |
| Límites físicos de texto | Respetar los tamaños aprobados; nunca truncar silenciosamente. Bloquear validación y conservar el dato fuente para revisión cuando exceda el límite. | Casos límite por cada campo restringido. |
| Referencia a usuario validador | `validated_by BIGINT UNSIGNED NULL`, FK restrictiva a `users.id`, sin `CASCADE` ni `SET NULL`. | FK e integridad histórica. La política real de eliminación de usuarios no está localizada en la evidencia revisada. |
| Identidad canónica | JSON UTF-8, sin BOM, orden de propiedades fijo, normalización NFC y SHA-256 hexadecimal minúsculo. | CAN-01 a CAN-12. |
| Unicidad de identidad | Índice único sobre `identity_key`, admitiendo múltiples valores `NULL`; identidades incompletas o ambiguas no se validan. | PA-04, PA-05, PA-12, PA-17, PA-24. |
| Inmutabilidad | Una versión validada no se edita directamente; la corrección genera una nueva versión. | PA-10, PA-11, PA-16, PA-23. |

## 4. Canonicalización: contrato de identidad

La identidad oficial se compone de país, autoridad estable, tipo de identificador, valor oficial, tipo de ámbito y valor del ámbito. `canonicalization_version` es metadato de formato, no un séptimo componente de negocio.

La versión 1 debe:

1. Validar el código de país y normalizarlo a mayúsculas.
2. Validar el ULID de la autoridad en forma canónica.
3. Recortar espacios externos y aplicar NFC a los textos indicados.
4. Preservar mayúsculas, puntuación, espacios internos y ceros iniciales del identificador oficial.
5. Admitir los tipos controlados `resolution`, `decree`, `agreement`, `circular`, `ordinance`, `law` y `other`. `other` requiere revisión autorizada antes de validar.
6. Rechazar caracteres de control y componentes requeridos vacíos.
7. Serializar JSON UTF-8 compacto, sin BOM, con orden fijo de propiedades.
8. Representar los nulos como JSON `null`, no como la cadena `"null"`.
9. Calcular SHA-256 sobre los bytes exactos serializados y guardar hexadecimal en minúsculas.
10. No fusionar identidades ambiguas ni recalcular claves existentes automáticamente.

Un cambio de canonicalización exige bloquear temporalmente las escrituras afectadas, calcular las claves candidatas, detectar colisiones y abortar sin migración parcial si aparece alguna. Solo se reanudan escrituras después de migrar y verificar todas las identidades completas. El índice único, por sí solo, no detecta equivalencias de negocio entre versiones distintas del algoritmo.

**CAN-01 a CAN-12:** se mantienen como aceptación obligatoria para la futura implementación. No se afirma que estén ejecutadas ni se declara verificado un hash de ejemplo sin reproducirlo.

## 5. Transacciones y auditoría

Las operaciones de dominio que deban auditarse utilizarán una única transacción en la misma conexión efectiva. La operación solo se considera exitosa si persisten tanto el cambio de dominio como su auditoría.

- Si falla la auditoría, se revierten los cambios de dominio.
- Si falla la escritura de dominio, no queda una auditoría que declare éxito.
- Las secuencias concurrentes se protegen con bloqueo transaccional y restricciones persistentes.
- Las pruebas de fallo deben inyectar errores reales y comprobar el estado final de las tablas.

La evidencia revisada muestra que `EloquentAuditRepository` persiste mediante `AuditLog::query()->create(...)` sin transacción explícita. La prueba existente de escritura/lectura no prueba atomicidad ante fallos. El caso de rollback de Campus demuestra un patrón transaccional en otro módulo, pero fuerza la base `cenicom` y no debe ejecutarse sin cambios contra la base aislada ACA-YEAR-02.

La integración de HT-02-B01 debe probar su propia atomicidad, sin modificar Institution ni Campus como parte de este contrato.

## 6. Autorización y arranque

La carga actual registra definiciones de permisos desde el registro de módulos y llama a sus métodos `register()`. En las definiciones y proveedores inspeccionados no se observaron escrituras de base de datos; el registro dinámico de proveedores sigue siendo una ruta indirecta de ejecución.

La prueba de arranque de permisos comprueba registro único y catálogo esperado; la prueba de aislamiento verifica el nombre de la base consultada. Ninguna sustituye las futuras pruebas de autorización por operación ni las pruebas de ausencia de escrituras fuera de la base aislada.

No se justifica modificar los proveedores globales, ModuleRegistry, PermissionDefinitionLoader o PermissionDefinitionBootstrapper sin una falla reproducible atribuible a ellos.

## 7. Estado de aceptación

| Prueba | Estado documental |
|---|---|
| B01-PA-01 a B01-PA-12 | Pendientes de implementación y ejecución específica de HT-02-B01. |
| B01-PA-13 y B01-PA-14 | Pendientes; requieren inyección de fallos y verificación de atomicidad. |
| B01-PA-15 | Pendiente; requiere probar operaciones no autorizadas sin cambios persistentes. |
| B01-PA-16 a B01-PA-24 | Pendientes de implementación y ejecución específica. |
| CAN-01 a CAN-12 | Pendientes de implementación y ejecución; no se consideran cubiertos por las pruebas de identidad de otros módulos. |
| Aislamiento de base de datos | Existe una prueba que verifica el nombre de la base; no demuestra por sí sola ausencia de toda escritura indirecta. |

La falta de pruebas ejecutadas no reabre las decisiones físicas aprobadas; establece la condición de aceptación de la futura implementación.

## 8. Secuencia de cierre y bloqueos

1. Consolidar esta matriz en el contrato HT-02-B01, sin alterar los documentos no rastreados ajenos al contrato.
2. Armonizar el estado del documento: diseño físico aprobado; análisis de impacto documentado; implementación y migraciones pendientes de autorización.
3. Mantener como dependencia pendiente la verificación de la política efectiva de eliminación de usuarios para la FK `validated_by`.
4. Declarar cerrado el análisis documental solo después de incorporar esta trazabilidad al contrato y resolver contradicciones de estado entre sus secciones.
5. No iniciar implementación, migraciones ni cambios de proveedores como consecuencia automática de este cierre.

## 9. Control de cambios

Las cinco matrices físicas aprobadas permanecen cerradas. Cualquier cambio posterior deberá identificar la contradicción o evidencia concreta que lo justifique, el impacto en las cinco tablas y las pruebas afectadas. No se reabre una decisión por preferencia estética, suposición o falta de implementación.

**Criterio final:** el contrato puede quedar documentalmente cerrado con pruebas futuras claramente identificadas, aunque estas todavía no estén implementadas. Ese cierre no equivale a autorizar el desarrollo.
/*/-*/*-/*-/-*/-*/-*/*-/*-/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/
No se ha podido confirmar la creación del ZIP y, por tanto, no puedo darte un enlace de descarga válido.

El contenido pendiente sigue definido: cinco migraciones PHP, siete tablas y un README con instrucciones para instalar y verificar los archivos exclusivamente en cenicom_aca_year02_isolated_test. No se han ejecutado migraciones ni se ha modificado la base de datos.
