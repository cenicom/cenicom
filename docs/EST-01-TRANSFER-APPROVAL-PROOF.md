# EST-01 — Evidencia física de solicitudes y autorizaciones de traslado institucional

**Identificador:** EST-01-TRANSFER-APPROVAL-PROOF  
**Estado:** Evidencia de pruebas físicas documentada  
**Motor:** MariaDB 10.4.32  
**Base de datos:** `est01_transfer_approval_proof`  
**Fecha de cierre documental:** 2 de noviembre de 2026

---

## 1. Objetivo

Documentar la comprobación física de las restricciones de integridad referencial, correspondencia institucional, unicidad y protección de registros para las tablas:

- `student_institution_transfer_requests`
- `student_institution_transfer_approvals`

El propósito es demostrar que las autorizaciones de traslado corresponden a las instituciones registradas en cada solicitud y que las relaciones no pueden romperse mediante operaciones de actualización o eliminación.

Esta evidencia se obtuvo en una base de datos aislada. No representa una migración de producción ni autoriza su implementación.

## 2. Entorno de pruebas

| Elemento | Valor |
|---|---|
| Motor | MariaDB |
| Versión | 10.4.32-MariaDB |
| Base aislada | `est01_transfer_approval_proof` |
| Tabla principal | `student_institution_transfer_requests` |
| Tabla dependiente | `student_institution_transfer_approvals` |

Las pruebas se realizaron directamente mediante sentencias SQL.

## 3. Diseño físico sometido a prueba

### 3.1. Tabla `student_institution_transfer_requests`

Se verificaron las siguientes claves candidatas compuestas:

| Restricción | Columnas |
|---|---|
| Clave primaria | `id` |
| UNIQUE | `(id, origin_institution_id)` |
| UNIQUE | `(id, destination_institution_id)` |

Estas claves permiten que las autorizaciones referencien la solicitud junto con la institución correspondiente.

### 3.2. Tabla `student_institution_transfer_approvals`

Se verificaron las siguientes restricciones:

| Restricción | Columnas o condición |
|---|---|
| Clave primaria | `id` |
| UNIQUE | `(request_id, institution_id)` |
| UNIQUE | `(request_id, institution_role)` |
| FK de origen | `(request_id, origin_institution_id)` |
| FK de destino | `(request_id, destination_institution_id)` |
| CHECK | `chk_approval_institution_role` |

Las dos claves foráneas utilizan:

- `ON UPDATE RESTRICT`
- `ON DELETE RESTRICT`

### 3.3. Regla de correspondencia institucional

El CHECK `chk_approval_institution_role` impone la correspondencia entre el rol y la institución:

**Autorización de origen:**
- `institution_role = 'origin'`.
- `origin_institution_id` no es NULL.
- `destination_institution_id` es NULL.
- `institution_id = origin_institution_id`.

**Autorización de destino:**
- `institution_role = 'destination'`.
- `destination_institution_id` no es NULL.
- `origin_institution_id` es NULL.
- `institution_id = destination_institution_id`.

La expresión fue consultada en `information_schema.CHECK_CONSTRAINTS`. La salida de phpMyAdmin presentó la expresión truncada, pero se verificó su longitud de 328 caracteres y se comprobaron sus condiciones mediante los ensayos de inserción.

## 4. Datos válidos utilizados

Se crearon dos solicitudes de prueba. Para la solicitud `...01` se insertaron las dos autorizaciones válidas:

| Campo | Autorización de origen | Autorización de destino |
|---|---|---|
| `request_id` | `...01` | `...01` |
| `institution_id` | `...11` | `...22` |
| `institution_role` | `origin` | `destination` |
| `status` | `pending` | `pending` |
| `origin_institution_id` | `...11` | NULL |
| `destination_institution_id` | NULL | `...22` |

Ambas inserciones fueron aceptadas.

La consulta final de verificación confirmó que permanecían exactamente esas dos autorizaciones para la solicitud `...01`, sin alteraciones ni registros parciales derivados de los intentos fallidos.

## 5. Pruebas ejecutadas

### 5.1. Inserción válida de autorizaciones

**Objetivo:** comprobar que se admiten las autorizaciones correctas de origen y destino.

**Resultado:** ambas inserciones fueron aceptadas.

**Conclusión:** las restricciones permiten registrar las dos autorizaciones válidas.

### 5.2. Rechazo por CHECK

**Objetivo:** comprobar que una autorización no puede declarar una institución incompatible con su rol.

**Resultado observado:**

```text
#4025 - CONSTRAINT chk_approval_institution_role failed
```

**Conclusión:** el CHECK rechaza la combinación institucional incorrecta.

### 5.3. FK compuesta de origen

**Objetivo:** comprobar que una autorización de origen no puede asociarse a una institución distinta de la institución de origen de la solicitud.

Se preparó una fila que satisfacía el CHECK, pero utilizaba una institución de origen que no correspondía a la solicitud.

**Resultado observado:**

```text
#1452 - Cannot add or update a child row
CONSTRAINT fk_approval_origin_request
```

**Conclusión:** la FK compuesta de origen rechaza la referencia incompatible.

### 5.4. FK compuesta de destino

**Objetivo:** comprobar que una autorización de destino no puede asociarse a una institución distinta de la institución de destino de la solicitud.

Se preparó una fila que satisfacía el CHECK, pero utilizaba una institución de destino que no correspondía a la solicitud.

**Resultado observado:**

```text
#1452 - Cannot add or update a child row
CONSTRAINT fk_approval_destination_request
```

**Conclusión:** la FK compuesta de destino rechaza la referencia incompatible.

### 5.5. Protección ON DELETE RESTRICT

**Objetivo:** comprobar que no se puede eliminar una solicitud que tiene autorizaciones dependientes.

**Operación:** intento de eliminar la solicitud `...01`.

**Resultado observado:**

```text
#1451 - Cannot delete or update a parent row
CONSTRAINT fk_approval_origin_request
```

**Conclusión:** la solicitud no puede eliminarse mientras existan autorizaciones relacionadas.

### 5.6. Unicidad por solicitud e institución

**Objetivo:** impedir dos autorizaciones para una misma institución dentro de una misma solicitud.

**Operación:** intento de insertar una segunda autorización de origen para la solicitud `...01` y la institución `...11`.

**Resultado observado:**

```text
#1062 - Duplicate entry
for key 'uq_approval_request_institution'
```

**Conclusión:** el índice UNIQUE `(request_id, institution_id)` impide duplicar la autorización por institución.

### 5.7. Verificación estructural del índice por rol

Se consultó `information_schema.STATISTICS`.

Resultado:

| Índice | Secuencia 1 | Secuencia 2 | NON_UNIQUE |
|---|---|---|---|
| `uq_approval_request_institution` | `request_id` | `institution_id` | 0 |
| `uq_approval_request_role` | `request_id` | `institution_role` | 0 |

**Conclusión:** ambos índices están creados como índices únicos y contienen las columnas y el orden definidos.

La prueba de duplicidad anterior fue rechazada por `uq_approval_request_institution`. No se atribuye ese resultado al índice por rol.

Debido a las restricciones CHECK y FK, no se aisló un ensayo de inserción que demuestre independientemente el rechazo por `uq_approval_request_role`. Su existencia, unicidad y composición sí quedaron verificadas estructuralmente.

### 5.8. Protección ON UPDATE RESTRICT de origen

**Objetivo:** impedir la modificación de la institución de origen de una solicitud referenciada por una autorización.

**Resultado observado:**

```text
#1451 - Cannot delete or update a parent row
CONSTRAINT fk_approval_origin_request
```

**Conclusión:** la FK de origen impide modificar la clave referenciada.

### 5.9. Protección ON UPDATE RESTRICT de destino

**Objetivo:** impedir la modificación de la institución de destino de una solicitud referenciada por una autorización.

**Resultado observado:**

```text
#1451 - Cannot delete or update a parent row
CONSTRAINT fk_approval_destination_request
```

**Conclusión:** la FK de destino impide modificar la clave referenciada.

## 6. Matriz consolidada

| N.º | Control | Evidencia | Resultado |
|---:|---|---|---|
| 1 | Inserción válida de origen | Inserción aceptada | Aprobado |
| 2 | Inserción válida de destino | Inserción aceptada | Aprobado |
| 3 | CHECK de correspondencia | Error `#4025` | Aprobado |
| 4 | FK compuesta de origen | Error `#1452` | Aprobado |
| 5 | FK compuesta de destino | Error `#1452` | Aprobado |
| 6 | ON DELETE RESTRICT | Error `#1451` | Aprobado |
| 7 | UNIQUE por solicitud e institución | Error `#1062` | Aprobado |
| 8 | UNIQUE por solicitud y rol | Estructura en `STATISTICS` | Verificado estructuralmente |
| 9 | ON UPDATE RESTRICT de origen | Error `#1451` | Aprobado |
| 10 | ON UPDATE RESTRICT de destino | Error `#1451` | Aprobado |
| 11 | Conservación de autorizaciones válidas | Consulta final de dos filas | Aprobado |

## 7. Integridad final de los datos de prueba

La consulta final sobre `student_institution_transfer_approvals`, filtrada por la solicitud `...01`, devolvió:

- Una autorización de origen.
- Una autorización de destino.
- Ambas en estado `pending`.
- Institución de origen `...11`.
- Institución de destino `...22`.
- Referencias institucionales coherentes con cada rol.

Los intentos rechazados no dejaron registros parciales.

Tampoco se modificaron las instituciones de origen y destino de la solicitud.

## 8. Limitaciones y alcance de esta evidencia

Esta prueba demuestra el comportamiento físico de las restricciones ensayadas en MariaDB 10.4.32.

No demuestra todavía:

1. La ejecución completa de un traslado institucional.
2. La creación transaccional del estudiante en la institución de destino.
3. El cierre de la afiliación de origen y la creación de la afiliación de destino.
4. La actualización de la solicitud a `completed`.
5. El rollback integral ante fallos durante la ejecución.
6. La autorización efectiva por usuario, rol e institución.
7. La auditoría histórica de decisiones y cambios.
8. El comportamiento de concurrencia del proceso completo.
9. La integración de estas tablas con las tablas definitivas de estudiantes y afiliaciones.

El índice `uq_approval_request_role` quedó verificado estructuralmente, no mediante una prueba independiente de rechazo.

## 9. Decisiones del contrato que permanecen vigentes

Esta evidencia no modifica las decisiones funcionales aprobadas:

- Cada solicitud requiere autorización de origen y destino.
- Las dos autorizaciones se crean inicialmente en estado `pending`.
- Cada institución resuelve únicamente su propia autorización.
- Una aprobación individual no habilita por sí sola la ejecución.
- Si cualquiera de las instituciones rechaza, la solicitud queda rechazada y no puede ejecutarse.
- La ejecución requiere las dos aprobaciones.
- La solicitud pasa a `completed` únicamente después de completar satisfactoriamente la transacción.
- Las solicitudes y sus decisiones se conservan como historial.

La auditoría detallada de las decisiones continúa pendiente de definición física.

## 10. Dictamen de cierre documental

Se considera cerrada la evidencia física de las restricciones ensayadas para las tablas de solicitudes y autorizaciones en el entorno aislado `est01_transfer_approval_proof`.

El cierre se limita a los controles enumerados en este documento y respeta las limitaciones expresamente indicadas.

**Este cierre no autoriza:**
- Crear migraciones de producción.
- Modificar la base de datos principal `cenicom`.
- Alterar las decisiones aprobadas del contrato EST-01.
- Reabrir ACA-YEAR-02.
- Declarar terminado el proceso completo de traslado institucional.

**Principio de trabajo conservado:** evidencia → contrato → impacto → corrección → evidencia.

**Estado final:** evidencia física de solicitudes y autorizaciones documentada; implementación pendiente de los contratos y pruebas restantes.
