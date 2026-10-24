# CENICOM ERP — HT-02-B01
## Contrato técnico consolidado y matriz de pruebas de aceptación

**Estado:** Contrato consolidado propuesto para cierre documental  
**Etapa:** Preparación de implementación  
**Referencia contractual:** HT-02-E03 y decisiones aprobadas de HT-02-B01  
**Principio de trabajo:** evidencia → contrato → impacto → corrección → evidencia

---

## 1. Objetivo

Establecer las garantías técnicas para registrar y mantener autoridades emisoras, publicaciones oficiales, versiones, períodos e hitos de publicaciones oficiales, preservando identidad, procedencia documental, integridad, auditoría y evolución histórica.

Este contrato separa las decisiones funcionales ya aprobadas de las verificaciones que deberán demostrar las pruebas. La aprobación de una regla contractual no equivale a demostrar que su implementación funciona.

## 2. Alcance

### 2.1 Incluido

La primera entrega contempla estas cinco tablas:

1. `academic_issuing_authorities`
2. `academic_official_publications`
3. `academic_official_publication_versions`
4. `academic_official_publication_periods`
5. `academic_official_publication_milestones`

Se incluyen:

- Identidad interna estable de la autoridad emisora.
- Identidad de la publicación separada de sus versiones.
- Procedencia documental y validación de versiones.
- Períodos e hitos variables definidos por la fuente oficial.
- Unicidad y protección frente a concurrencia.
- Inmutabilidad de versiones validadas.
- Auditoría de las operaciones que cambian el estado persistente.
- Autorización de operaciones protegidas.
- Pruebas de persistencia, integridad, autorización, concurrencia y atomicidad.

### 2.2 Fuera de alcance

No se implementarán en esta entrega:

- Asignación de publicaciones a años lectivos.
- Administración integral de años académicos.
- Administración de instituciones o sedes.
- Implementación completa de compatibilidad y asignaciones.
- Flujos completos de retiro de publicaciones y versiones.
- Interfaz de usuario y navegación.
- Integración automática con organismos reguladores.
- Modificación del Generator, de tablas geográficas importadas o de proveedores globales sin evidencia específica que la justifique.

El diseño deberá permitir la evolución posterior sin implementar anticipadamente estos flujos.

## 3. Convenciones estructurales

### CT-B01-01. Identificadores y relaciones

- Los identificadores internos de las cinco tablas serán `CHAR(26)` compatibles con ULID.
- Las relaciones entre las tablas de B01 utilizarán claves foráneas explícitas.
- Las restricciones e índices deberán corresponder a las reglas contractuales.
- No se modificarán tablas geográficas importadas para acomodar B01.
- No se declarará una clave foránea hacia `countries.iso2` sin evidencia de una clave única compatible en la tabla de referencia.
- No se supondrá que existe un módulo académico implementado antes de verificar los archivos y convenciones reales del repositorio.

**Aceptación:** las cinco tablas permiten identificar sus registros y relaciones sin ambigüedad, y las restricciones de integridad están verificadas en la base aislada.

### CT-B01-02. Identidad de la autoridad emisora

- Cada autoridad tiene un identificador interno estable.
- El nombre es descriptivo; no constituye por sí solo la identidad.
- Cambiar el nombre no crea automáticamente una autoridad nueva.
- Reorganizaciones, sucesiones o cambios de competencias que puedan alterar la identidad requieren revisión autorizada.
- No se fusionan autoridades por inferencia ni por coincidencia de nombres.

**Aceptación:** el cambio de nombre conserva el identificador interno. Los casos de sucesión dudosa no se resuelven automáticamente.

### CT-B01-03. Identidad de la publicación

La identidad contractual se compone de:

1. País.
2. Autoridad emisora identificada mediante su identificador interno estable.
3. Tipo de identificador oficial.
4. Número o valor del identificador oficial.
5. Tipo de ámbito de referencia: `not_applicable`, `year` o `period`.
6. Valor del ámbito cuando corresponda.

El período de referencia no se supondrá equivalente al año calendario.

La identidad de publicación es independiente de la identidad de sus versiones.

#### Regla de identidad incompleta

- Una identidad incompleta puede conservarse para revisión si el flujo de creación lo permite.
- Su `identity_key` permanece `NULL` mientras no exista identidad completa.
- Una identidad incompleta o ambiguamente identificada no puede validarse ni asignarse a un año académico.
- No se utilizarán valores predeterminados arbitrarios para completar datos oficiales ausentes.
- Una equivalencia dudosa requiere revisión autorizada; no se fusionarán registros automáticamente.
- Los datos incompletos no deben permitir eludir la unicidad de una identidad completa.

### CT-B01-04. Clave canónica de identidad y unicidad

La clave `identity_key` se calculará mediante SHA-256 sobre una representación canónica, determinista y versionada de los componentes completos de identidad establecidos en CT-B01-03.

La representación deberá cumplir estas reglas:

- Serialización inequívoca mediante campos identificados, evitando ambigüedad por concatenación.
- Normalización explícita, documentada y determinista.
- No eliminar puntuación ni modificar el significado de identificadores oficiales sin una regla autorizada y documentada.
- Persistir la clave calculada cuando la identidad esté completa.
- Mantener `identity_key` en `NULL` cuando la identidad esté incompleta.
- Aplicar una restricción única en la persistencia para impedir duplicados concurrentes.
- Tratar un conflicto de unicidad como resultado controlado, sin comunicar éxito.

La versión de canonicalización deberá quedar identificada y documentada.

Un cambio de algoritmo no podrá habilitar duplicados de una misma identidad contractual. Antes de cambiar la canonicalización se deberán detectar posibles colisiones, definir la migración de claves y verificar el conjunto completo de identidades afectadas.

**Aceptación:** las identidades equivalentes conforme a las reglas explícitas producen la misma clave; identidades diferentes no se fusionan por inferencia; la persistencia rechaza duplicados incluso ante concurrencia.

### CT-B01-05. Identidad y ciclo de vida de versiones

- Cada versión tiene un identificador interno único e inmutable.
- El número oficial de versión se conserva como dato independiente cuando exista.
- La ausencia de número oficial no impide por sí sola crear una versión interna.
- Cada versión pertenece a una publicación determinada.
- La versión conserva la información documental y temporal que le corresponde.
- Una versión validada o disponible es inmutable.
- Una corrección genera una nueva versión; no sobrescribe una versión validada.
- Las referencias históricas deberán poder identificar exactamente la versión utilizada.

**Aceptación:** se conservan las versiones anteriores y no se permite modificar directamente una versión validada.

### CT-B01-06. Procedencia y validación documental

Una versión deberá disponer de información suficiente para comprobar:

- La identificación oficial de la publicación.
- La procedencia documental.
- La evidencia disponible para su revisión.
- La identidad y el resultado de la operación de validación.
- La versión exacta sometida a validación.

Una URL aislada no se considerará evidencia suficiente. No será obligatorio adjuntar una copia binaria del documento en todos los casos.

La validación deberá impedir que una identidad incompleta, una identidad ambigua sin resolver o una versión con información obligatoria insuficiente quede disponible.

La validación será una operación autorizada y auditable.

**Aceptación:** los casos con evidencia insuficiente son rechazados; los casos completos pasan únicamente después de satisfacer las reglas y autorización aplicables.

### CT-B01-07. Períodos oficiales variables

Los períodos se almacenarán como registros independientes asociados a una versión exacta.

- No se codificará una cantidad fija de períodos.
- Cada período conservará, como mínimo, su nombre, orden y fechas oficiales de inicio y fin.
- La validación comprobará las reglas temporales y el orden exigido por el contrato.
- Los períodos de una versión validada no se modificarán directamente.
- Las correcciones se incorporarán mediante una nueva versión.

La estructura no supondrá que todas las publicaciones tienen cuatro períodos ni que los períodos utilizan siempre la misma denominación.

**Aceptación:** se pueden representar cantidades variables de períodos y verificar sus fechas y orden sin introducir una cantidad fija en el código.

### CT-B01-08. Hitos oficiales

Los hitos pertenecerán a una versión exacta y tendrán un tipo temporal explícito: `point` o `interval`.

Reglas temporales:

- Para `point`, `starts_on` es obligatorio y `ends_on` debe ser `NULL`.
- Para `interval`, `starts_on` y `ends_on` son obligatorios, y `ends_on` debe ser igual o posterior a `starts_on`.
- No se inventarán fechas para hitos opcionales.
- Los hitos obligatorios definidos por la publicación deberán existir antes de validar la versión.
- Se comprobará la coherencia temporal con el ámbito y las reglas oficiales de la versión.
- No se exigirá que todos los hitos generales del calendario estén dentro de un período académico individual si la fuente oficial los define para el calendario completo.
- Los hitos de una versión validada serán inmutables.

**Aceptación:** las combinaciones temporales inválidas son rechazadas y no se puede validar una versión que omita hitos obligatorios aplicables.

### CT-B01-09. Compatibilidad futura

La estructura deberá permitir evaluar posteriormente la compatibilidad por:

- País.
- Autoridad o regulador.
- Régimen.
- Región.
- Tipo de calendario A/B.

B01 no implementará todas las reglas de compatibilidad ni las asignaciones a años académicos.

No se asumirá que la autoridad de registro de una institución sea necesariamente la autoridad emisora de una publicación. Tampoco se utilizará una configuración global como prueba suficiente de la compatibilidad de una publicación concreta.

**Aceptación:** la estructura puede representar los datos necesarios sin inferir compatibilidad ni imponer un calendario global.

### CT-B01-10. Retiro e historial futuro

El diseño deberá preservar estas reglas para bloques posteriores:

- Retirar una versión impide nuevas asignaciones a esa versión, sin modificar referencias históricas existentes.
- Retirar una publicación impide nuevas asignaciones de sus versiones, sin alterar años académicos ya vinculados.
- Una corrección genera una versión nueva.
- Una identidad dudosa requiere resolución autorizada.
- Un año académico deberá poder referenciar una versión exacta.

Los flujos completos de retiro, resolución y asignación no forman parte de la primera entrega.

### CT-B01-11. Autorización

La arquitectura de seguridad actual distingue entre el registro de definiciones de permisos y la asignación efectiva de permisos a una identidad.

La evidencia revisada muestra que:

- `PermissionDefinitionLoader` recoge definiciones declaradas por los módulos.
- `PermissionDefinitionBootstrapper` ejecuta las definiciones registradas.
- `IdentityService` obtiene permisos directos y heredados de las relaciones Eloquent del usuario autenticado.
- Registrar una definición no concede automáticamente ese permiso a ningún usuario.

En consecuencia:

- Cada operación protegida deberá verificar autorización en el límite de ejecución.
- No se considerará suficiente ocultar controles de interfaz.
- No se considerarán suficientes permisos genéricos de otros módulos.
- La falta de autorización deberá impedir cambios persistentes.
- Las operaciones sensibles, especialmente la validación, deberán tener permisos diferenciados conforme al contrato general.

**Pendiente bloqueante para implementación funcional:** aprobar los nombres exactos de los permisos de B01 y su asignación a las operaciones. No se inventarán nombres ni se concederán permisos por conveniencia.

**Aceptación:** pruebas negativas demuestran que una identidad sin el permiso requerido no puede ejecutar la operación protegida.

### CT-B01-12. Auditoría y atomicidad

Las operaciones que modifican datos de dominio y requieren auditoría deberán registrar ambas partes dentro de una misma transacción efectiva.

Se reutilizará el contrato existente `AuditRecorderInterface` y los DTO compartidos de auditoría cuando sean compatibles con la operación.

Reglas obligatorias:

1. La escritura de dominio y la escritura de auditoría deben utilizar la misma conexión transaccional.
2. La transacción deberá abarcar ambas escrituras.
3. Si falla la escritura de dominio, no debe persistir un registro de auditoría que declare éxito de la operación.
4. Si falla la escritura de auditoría, la escritura de dominio deberá revertirse.
5. No se declarará éxito antes de completar la transacción.
6. Los eventos y efectos externos se ejecutarán después del compromiso o mediante un mecanismo transaccional compatible.
7. Las pruebas deberán inducir fallos reales y comprobar el estado persistido después de la reversión.

El hecho de que `AuditLog` y `EloquentAuditRepository` utilicen Eloquent sin declarar una conexión explícita no demuestra, por sí solo, que cualquier operación futura comparta la misma conexión.

**Aceptación:** una prueba sobre la base aislada induce un fallo del registrador de auditoría y demuestra que el registro de dominio no persiste. Otra prueba demuestra que un fallo de la escritura principal no genera un resultado de éxito falso.

### CT-B01-13. Auditoría verificable

Las operaciones auditables registrarán, según corresponda:

- Actor.
- Identificador y condición de autenticación del actor.
- Acción.
- Sujeto afectado.
- Resultado.
- Fecha y hora.
- Metadatos relevantes para reconstruir la operación.

Las operaciones de validación deberán permitir identificar la publicación y versión afectadas. Las correcciones futuras conservarán los valores anteriores y posteriores exigidos por el contrato general.

**Aceptación:** la prueba verifica los datos del registro de auditoría y su coherencia con el resultado de la operación.

## 4. Reglas de persistencia y concurrencia

### CT-B01-14. Integridad persistente

Las reglas esenciales no dependerán exclusivamente de validaciones de interfaz o de comprobaciones previas a la escritura.

Las restricciones de la base deberán proteger, según corresponda:

- Identificadores únicos.
- Relaciones válidas entre las cinco tablas.
- Unicidad de la identidad canónica completa.
- Integridad de los valores obligatorios.
- Coherencia de los estados y datos temporales cuando pueda expresarse mediante restricciones.
- Inmutabilidad de versiones validadas mediante el servicio y los límites de persistencia correspondientes.

### CT-B01-15. Operaciones concurrentes

La unicidad deberá seguir garantizada si dos operaciones intentan registrar simultáneamente la misma identidad.

Una comprobación previa con `SELECT` no sustituye la restricción única persistente.

Los conflictos de concurrencia se tratarán de forma controlada. No se resolverán como éxito silencioso ni mediante la creación automática de otra publicación equivalente.

**Aceptación:** una prueba de concurrencia con persistencia real compatible demuestra que solo se registra una identidad canónica.

## 5. Pruebas de aceptación

| ID | Escenario | Resultado esperado |
|---|---|---|
| B01-PA-01 | Crear una autoridad válida | Identificador estable y persistencia correcta |
| B01-PA-02 | Cambiar el nombre de una autoridad | Conserva la identidad interna |
| B01-PA-03 | Crear una publicación con identidad completa | Persiste con la autoridad correcta |
| B01-PA-04 | Crear una identidad duplicada | La restricción persistente rechaza el duplicado |
| B01-PA-05 | Crear identidades distintas | No se fusionan por inferencia |
| B01-PA-06 | Registrar una versión sin número oficial | Se admite un identificador interno |
| B01-PA-07 | Crear varias versiones | Cada versión queda identificada independientemente |
| B01-PA-08 | Validar con procedencia insuficiente | La validación se rechaza |
| B01-PA-09 | Validar con procedencia suficiente | El resultado y la auditoría se persisten correctamente |
| B01-PA-10 | Modificar una versión validada | La modificación directa se rechaza |
| B01-PA-11 | Corregir una versión validada | Se crea otra versión y se conserva la anterior |
| B01-PA-12 | Crear identidad duplicada concurrentemente | Solo se acepta una identidad |
| B01-PA-13 | Forzar fallo de auditoría | Se revierte la escritura de dominio |
| B01-PA-14 | Forzar fallo de escritura de dominio | No queda un registro de auditoría de éxito |
| B01-PA-15 | Ejecutar operación sin permiso | No hay cambio persistente no autorizado |
| B01-PA-16 | Consultar una versión histórica | Se conserva su identidad y contenido validado |
| B01-PA-17 | Validar una identidad ambigua sin resolver | Se impide su disponibilidad |
| B01-PA-18 | Verificar compatibilidad futura | Los datos necesarios se pueden representar sin inferencias globales |
| B01-PA-19 | Validar período con fechas incoherentes | La validación se rechaza |
| B01-PA-20 | Validar versión sin hitos obligatorios | La validación se rechaza |
| B01-PA-21 | Validar hito de tipo `point` con fecha final | Se rechaza la combinación temporal |
| B01-PA-22 | Validar hito de tipo `interval` sin fechas completas | Se rechaza la combinación temporal |
| B01-PA-23 | Modificar hitos de una versión validada | La modificación directa se rechaza |
| B01-PA-24 | Cambiar la versión de canonicalización | No se habilitan duplicados por cambio de algoritmo |

### Criterio global de aceptación

B01 no se considerará implementado y terminado hasta que:

1. Las cinco tablas aprobadas estén definidas y verificadas.
2. Las restricciones e índices protejan las reglas contractuales.
3. La unicidad esté garantizada en persistencia y probada bajo concurrencia.
4. La inmutabilidad de versiones validadas esté protegida y probada.
5. Los períodos y los hitos variables cumplan sus reglas temporales.
6. Las operaciones protegidas comprueben la autorización efectiva.
7. La atomicidad de dominio y auditoría esté demostrada mediante fallos inducidos.
8. Las pruebas se ejecuten contra la base aislada autorizada.
9. La matriz de trazabilidad relacione cada regla con evidencia concreta.
10. No se hayan modificado frentes ajenos sin justificación documentada.

## 6. Evidencia técnica disponible

La evidencia revisada confirma:

- Existencia de `AuditRecorderInterface`, `AuditRecorder` y `EloquentAuditRepository`.
- Persistencia de auditoría mediante el modelo `AuditLog`.
- Uso de `DB::transaction()` en el caso de uso de creación de sedes.
- Resolución de permisos directos y heredados en `IdentityService`.
- Registro de definiciones de permisos por módulo y almacenamiento de ese catálogo en memoria.
- Una guardia de pruebas que verifica la configuración efectiva y el nombre de la base aislada.
- Ausencia de evidencia presentada hasta el momento de migraciones y modelos académicos ya implementados.

Esta evidencia identifica componentes reutilizables y patrones existentes, pero no demuestra todavía la atomicidad de B01, la concurrencia de su clave canónica ni la inmutabilidad de sus futuras versiones.

## 7. Control de la base aislada

Las pruebas de B01 deberán utilizar la configuración aislada aprobada:

- Base: `cenicom_aca_year02_isolated_test`.
- Host: `127.0.0.1`.
- Puerto: `3308`.
- Usuario: `aca_year02_test`.
- Conexión predeterminada efectiva: `mysql`.
- Entorno: `testing`.

La guardia deberá permanecer activa. Las pruebas no podrán sustituirla por una configuración que apunte a la base ordinaria del proyecto.

No se copiarán sin adaptación pruebas existentes que utilicen `RefreshDatabase`, `migrate:fresh` o una conexión fija a `cenicom`.

No se ejecutarán migraciones hasta aprobar el impacto técnico, verificar el estado de la base aislada y autorizar expresamente ese paso.

## 8. Pendientes de verificación y decisiones bloqueantes

| Elemento | Estado |
|---|---|
| Componentes de identidad y versionado | Decisión contractual definida |
| Canonicalización y unicidad de identidad | Decisión contractual definida |
| Tipos temporales de hitos | Decisión contractual definida |
| Atomicidad dominio-auditoría | Requisito definido; prueba pendiente |
| Concurrencia de identidad canónica | Requisito definido; prueba pendiente |
| Inmutabilidad persistente | Requisito definido; prueba pendiente |
| Permisos concretos de B01 | Nombres y asignación pendientes de aprobación |
| Diseño físico de columnas e índices | Debe quedar documentado y contrastado antes de migrar |
| Integración con el manifiesto y proveedor del módulo | Verificar el patrón real antes de crear archivos |
| Ejecución de migraciones | No autorizada por este documento |

## 9. Secuencia autorizable

1. Revisar y aprobar esta versión consolidada del contrato.
2. Cerrar la nomenclatura de permisos y su relación con las operaciones protegidas.
3. Aprobar el diseño físico de las cinco tablas: columnas, tipos, restricciones, índices y claves foráneas.
4. Presentar la matriz de impacto sobre archivos existentes y nuevos.
5. Verificar que la base aislada está autorizada y en condiciones de recibir las migraciones.
6. Solicitar autorización expresa para crear y ejecutar las migraciones.
7. Implementar las cinco tablas y los componentes mínimos aprobados.
8. Implementar y ejecutar las pruebas aisladas de integridad, autorización, inmutabilidad, concurrencia y atomicidad.
9. Presentar evidencia de los resultados antes de abrir el siguiente bloque.

## 10. Declaración de control de cambios

Este contrato no modifica por sí mismo el código ni la base de datos.

La matriz HT-02-E03 continúa siendo la referencia de trazabilidad contractual. Las decisiones de HT-02-B01 se incorporan aquí sin convertir pruebas pendientes en garantías ya demostradas.

**Estado:** versión consolidada propuesta; pendiente de aprobación documental y cierre de los bloqueos explícitos de implementación.

**Principio rector:** evidencia → contrato → impacto → corrección → evidencia.

**¡Primero el contrato; después el código!**
