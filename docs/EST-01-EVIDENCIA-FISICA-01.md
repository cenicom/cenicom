# EST-01-EVIDENCIA-FISICA-01

**Proyecto:** CENICOM ERP\
**Frente:** EST-01 --- Módulo Student\
**Tipo de evidencia:** Inspección física de esquema, solo lectura\
**Base inspeccionada:** `cenicom`\
**Motor/versionado informado:** MariaDB `10.4.32-MariaDB`\
**Estado:** Evidencia inicial reunida; análisis de compatibilidad
pendiente\
**Alcance:** Metadatos de tablas, columnas, índices y claves foráneas.
No se modificaron datos ni objetos.

## 1. Objetivo

Registrar la evidencia obtenida directamente de `information_schema`
para las tablas existentes que condicionan el diseño físico de EST-01.
Esta nota no autoriza migraciones ni cambios de esquema.

## 2. E01 --- Motor y base seleccionada

Consulta ejecutada:

``` sql
SELECT VERSION() AS mariadb_version, DATABASE() AS base_actual;
```

Resultado:

  Campo                               Valor
  ----------------------------------- -------------------
  `mariadb_version`                   `10.4.32-MariaDB`
  `base_actual` en esa comprobación   `cenicom`

**Nota de contexto:** una primera consulta se ejecutó con
`information_schema` como base seleccionada. Para las inspecciones
posteriores se usó `TABLE_SCHEMA = 'cenicom'` explícitamente, lo que
permitió recuperar los metadatos correctos aunque `DATABASE()` siguiera
mostrando `information_schema`.

## 3. E02 --- Existencia y motor

La consulta dirigida a `information_schema.TABLES`, con
`TABLE_SCHEMA = 'cenicom'`, encontró:

  -----------------------------------------------------------------------
  Tabla                   Motor                   Resultado
  ----------------------- ----------------------- -----------------------
  `addresses`             InnoDB                  Encontrada

  `campuses`              InnoDB                  Encontrada

  `genders`               InnoDB                  Encontrada

  `institutions`          InnoDB                  Encontrada

  `typedocuments`         InnoDB                  Encontrada

  `academic_years`        ---                     No encontrada en
                                                  `cenicom` durante esta
                                                  inspección
  -----------------------------------------------------------------------

La ausencia de `academic_years` en este resultado no prueba que la
migración no exista en el código. Solo prueba que la tabla no fue
encontrada en el esquema inspeccionado en ese momento.

## 4. E03 --- Columnas físicas

### 4.1 `addresses`

  Columna          Tipo                      Nulo Colación / notas
  ---------------- ----------------------- ------ ----------------------
  `id`             `char(26)`                  No `utf8mb4_unicode_ci`
  `country_id`     `bigint(20) unsigned`       No Sin colación
  `state_id`       `bigint(20) unsigned`       No Sin colación
  `city_id`        `bigint(20) unsigned`       No Sin colación
  `address`        `varchar(255)`              Sí `utf8mb4_unicode_ci`
  `neighborhood`   `varchar(255)`              Sí `utf8mb4_unicode_ci`
  `created_at`     `timestamp`                 Sí ---
  `updated_at`     `timestamp`                 Sí ---

`countries`, `states` y `cities` son catálogos geográficos importados;
no se deben inventar migraciones para ellos.

### 4.2 `institutions`

  -------------------------------------------------------------------------------------------------
  Columna                             Tipo                              Nulo Predeterminado /
                                                                             colación
  ----------------------------------- ---------------- --------------------- ----------------------
  `id`                                `char(26)`                          No `utf8mb4_unicode_ci`

  `code`                              `varchar(20)`                       No `utf8mb4_unicode_ci`

  `short_code`                        `varchar(15)`                       No `utf8mb4_unicode_ci`

  `name`                              `varchar(255)`                      No `utf8mb4_unicode_ci`

  `official_registration_country`     `varchar(2)`                        Sí `utf8mb4_unicode_ci`

  `official_registration_authority`   `varchar(150)`                      Sí `utf8mb4_unicode_ci`

  `official_registration_value`       `varchar(100)`                      Sí `utf8mb4_unicode_ci`

  `status`                            `varchar(30)`                       No `'draft'`,
                                                                             `utf8mb4_unicode_ci`

  `created_at`                        `timestamp`                         Sí ---

  `updated_at`                        `timestamp`                         Sí ---
  -------------------------------------------------------------------------------------------------

### 4.3 `campuses`

  Columna            Tipo               Nulo Predeterminado / colación
  ------------------ ---------------- ------ ---------------------------------
  `id`               `char(26)`           No `utf8mb4_unicode_ci`
  `institution_id`   `char(26)`           No `utf8mb4_unicode_ci`
  `code`             `varchar(40)`        No `utf8mb4_unicode_ci`
  `short_code`       `varchar(15)`        No `utf8mb4_unicode_ci`
  `name`             `varchar(255)`       No `utf8mb4_unicode_ci`
  `ministry_code`    `varchar(50)`        Sí `utf8mb4_unicode_ci`
  `address_id`       `char(26)`           No `utf8mb4_unicode_ci`
  `status`           `varchar(30)`        No `'draft'`, `utf8mb4_unicode_ci`
  `created_at`       `timestamp`          Sí ---
  `updated_at`       `timestamp`          Sí ---

### 4.4 `genders`

  Columna        Tipo                      Nulo Predeterminado / notas
  -------------- ----------------------- ------ ------------------------
  `id`           `bigint(20) unsigned`       No `auto_increment`
  `name`         `varchar(50)`               No `utf8mb4_unicode_ci`
  `created_at`   `timestamp`                 Sí `current_timestamp()`
  `updated_at`   `timestamp`                 Sí `current_timestamp()`

### 4.5 `typedocuments`

  Columna        Tipo                      Nulo Predeterminado / notas
  -------------- ----------------------- ------ ------------------------
  `id`           `bigint(20) unsigned`       No `auto_increment`
  `name`         `varchar(50)`               No `utf8mb4_unicode_ci`
  `created_at`   `timestamp`                 Sí `current_timestamp()`
  `updated_at`   `timestamp`                 Sí `current_timestamp()`

### 4.6 Observaciones comunes

-   Los identificadores de `institutions`, `campuses` y `addresses` son
    `CHAR(26)`.
-   Los identificadores de `genders` y `typedocuments` son
    `BIGINT UNSIGNED AUTO_INCREMENT`.
-   Las columnas de texto inspeccionadas usan `utf8mb4_unicode_ci`.
-   La evidencia no incluye filas de datos de los catálogos ni autoriza
    alterarlos.

## 5. E04 --- Índices existentes

  -----------------------------------------------------------------------------------------------------
  Tabla             Índice                                        Unicidad          Columnas en orden
  ----------------- --------------------------------------------- ----------------- -------------------
  `addresses`       `PRIMARY`                                     Único             `id`

  `campuses`        `PRIMARY`                                     Único             `id`

  `campuses`        `campuses_address_id_index`                   No único          `address_id`

  `campuses`        `campuses_code_unique`                        Único             `code`

  `campuses`        `campuses_institution_id_short_code_unique`   Único compuesto   `institution_id`,
                                                                                    `short_code`

  `genders`         `PRIMARY`                                     Único             `id`

  `genders`         `name`                                        Único             `name`

  `institutions`    `PRIMARY`                                     Único             `id`

  `institutions`    `institutions_code_unique`                    Único             `code`

  `institutions`    `institutions_short_code_unique`              Único             `short_code`

  `typedocuments`   `PRIMARY`                                     Único             `id`

  `typedocuments`   `name`                                        Único             `name`
  -----------------------------------------------------------------------------------------------------

**Implicaciones:** - `institutions.short_code` es único globalmente. -
`campuses` garantiza unicidad global de `code` y unicidad de
`short_code` dentro de la institución. - No se observó un índice único
compuesto `(institution_id, id)` en `campuses`. - Estos índices no
establecen las reglas de unicidad de documentos, códigos o afiliaciones
de estudiantes; las tablas correspondientes aún no forman parte de esta
evidencia.

## 6. E05 --- Claves foráneas existentes

  -----------------------------------------------------------------------------------------------------
  Tabla origen   Restricción                         Columna origen     Tabla destino    Columna
                                                                                         destino
  -------------- ----------------------------------- ------------------ ---------------- --------------
  `campuses`     `campuses_address_id_foreign`       `address_id`       `addresses`      `id`

  `campuses`     `campuses_institution_id_foreign`   `institution_id`   `institutions`   `id`
  -----------------------------------------------------------------------------------------------------

No se devolvieron otras claves foráneas para las cinco tablas
inspeccionadas.

**Implicación:** las FK actuales validan individualmente la institución
del campus y su dirección. No garantizan por sí solas una
correspondencia compuesta entre institución y campus en una futura
afiliación estudiantil.

## 7. Decisiones EST-01 relacionadas con esta evidencia

### TD-01 --- Normalización documental aprobada

-   Conservar el documento original.
-   Crear un valor normalizado para búsqueda y comparación de
    duplicados.
-   Normalizar solo recortando espacios exteriores y convirtiendo letras
    a mayúsculas.
-   No eliminar automáticamente guiones, signos ni caracteres internos.
-   La unicidad documental es por institución y tipo de documento.
-   Antes de elegir la colación del campo normalizado, comprobar el
    comportamiento real de MariaDB 10.4.32 para que la comparación no
    introduzca equivalencias no autorizadas.

### TD-02 --- Historial de códigos

Decisión aprobada: conservar cada asignación de código como registro
histórico; un solo código vigente por estudiante; los códigos anteriores
no se sobrescriben ni reutilizan dentro de la institución. La
sustitución provisional por definitiva conserva ambos registros. Si el
documento definitivo ya pertenece a otro registro, bloquear la
sustitución automática y exigir revisión autorizada. El traslado de
campus no cambia el código.

### TD-03 --- Afiliación activa única

Decisión aprobada: conservar el historial de afiliaciones; un estudiante
tiene una sola afiliación activa. El traslado cierra la anterior y crea
la nueva dentro de una transacción, después de validar que el campus de
destino pertenece a la institución del estudiante. La operación debe
soportar concurrencia y revertirse completa si falla. No se reasignan
automáticamente matrículas, calificaciones ni registros históricos.

### TD-04 --- Historial fotográfico aprobado

Conservar cada fotografía como registro independiente, sin sobrescribir
fotografías anteriores. Puede vincularse opcionalmente al año lectivo y
a la afiliación; guardar ruta de archivo, fecha de captura, tipo
(identificación/anual) e indicador de principal. Los archivos deben
almacenarse fuera de la base de datos y tener acceso protegido.

## 8. Pendientes técnicos, en orden

1.  **Comparación y colación documental:** probar explícitamente
    comparaciones en MariaDB 10.4.32 con valores de prueba
    representativos (mayúsculas/minúsculas, espacios exteriores, guiones
    y signos internos), sin alterar tablas existentes. Determinar cómo
    se aplicará la normalización y qué colación permite cumplir la
    unicidad aprobada.
2.  **Un código vigente y una afiliación activa:** verificar en una base
    aislada de prueba qué restricciones soporta MariaDB 10.4.32,
    incluidos índices únicos sobre columnas generadas que devuelven
    `NULL` para registros históricos. La restricción de base de datos
    debe complementar las transacciones y bloqueos; no sustituirlos.
3.  **Disponibilidad del año lectivo:** comprobar `migrate:status` en el
    entorno aislado y verificar el estado de las migraciones académicas.
    `academic_years` no fue encontrada en `cenicom` durante esta
    inspección. No ejecutar migraciones en la base `cenicom` como parte
    de esta verificación.

## 9. Límites de esta evidencia

-   Es una transcripción estructurada de los resultados compartidos
    desde phpMyAdmin; no es una conexión directa del asistente a la base
    de datos.
-   Los metadatos reflejan el estado observado durante la sesión de
    inspección; deben volver a consultarse si el esquema cambia.
-   No se ejecutaron `CREATE`, `ALTER`, `DROP`, `INSERT`, `UPDATE` ni
    `DELETE`.
-   Esta evidencia no autoriza la implementación de migraciones de
    EST-01.

**Actualización de estado posterior a E06 y E07:**

La tabla anterior conserva el estado registrado durante la inspección física inicial E01–E05. Las verificaciones experimentales posteriores se documentan por separado:

- E06: aprobada para historial, afiliación activa única, transacciones, rollback y concurrencia.
- E07: aprobada para integridad referencial compuesta y pertenencia institucional entre estudiante, campus e institución.

Los resultados posteriores no modifican retroactivamente el registro histórico de E01–E05.
## 10. Estado

  -----------------------------------------------------------------------
  Evidencia                           Estado
  ----------------------------------- -----------------------------------
  E01 --- motor/conexión              Confirmada

  E02 --- existencia/motor            Confirmada para cinco tablas;
                                      `academic_years` no encontrada

  E03 --- columnas                    Confirmada para las cinco tablas

  E04 --- índices                     Confirmada

  E05 --- claves foráneas             Confirmada

  Pendiente 1 --- colación documental Por verificar

  Pendiente 2 --- unicidad en MariaDB Por verificar en entorno aislado
  10.4.32                             

  Pendiente 3 --- migraciones         Por verificar en entorno aislado
  académicas                          
  -----------------------------------------------------------------------

**Conclusión:** E01--E05 documentan la estructura física observada. El
siguiente trabajo debe seguir el orden de los tres pendientes técnicos y
permanecer en modo de inspección/prueba aislada hasta que exista
evidencia suficiente para fijar el diseño físico de EST-01.
/*/-*/-*/-*/-*/-*/*-/-*/*-/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/*-/-*/-*/-*/-*/-*/*-/-*/-*/*-/*-
## 11. E06 — Verificación experimental de afiliación activa y concurrencia

**Proyecto:** CENICOM ERP  
**Frente:** EST-01 — Módulo Student  
**Tipo de evidencia:** Prueba experimental de integridad, transacciones y concurrencia  
**Base utilizada:** `cenicom_aca_year02_isolated_test`  
**Motor:** MariaDB `10.4.32-MariaDB`  
**Tabla:** `est01_student_affiliations_test`  
**Estado:** Pruebas experimentales aprobadas; validación de pertenencia institucional pendiente.

### 11.1 Objetivo

Comprobar experimentalmente que MariaDB 10.4.32 permite garantizar una sola afiliación activa por estudiante, conservar el historial, ejecutar traslados transaccionales y proteger la restricción ante dos sesiones concurrentes.

Las pruebas se ejecutaron exclusivamente en la base aislada. No se modificó la base principal `cenicom`.

### 11.2 Estructura experimental evaluada

La tabla utiliza una columna generada persistente:

`current_student_id`

Su expresión devuelve el identificador del estudiante cuando `status = 'active'` y `NULL` para los registros históricos.

Se definió el índice:

`uq_est01_affiliation_current_student`

como índice único sobre `current_student_id`.

Este mecanismo permite múltiples afiliaciones históricas para un estudiante, pero impide que tenga más de una activa.

### 11.3 Resultados

| Prueba | Resultado | Estado |
|---|---|---|
| Varias afiliaciones históricas para un estudiante | Permitidas | Aprobada |
| Una afiliación activa para el estudiante | Permitida | Aprobada |
| Segunda afiliación activa para el mismo estudiante | Rechazada por error 1062 | Aprobada |
| Traslado dentro de una transacción | Actualización de la anterior e inserción de la nueva confirmadas mediante `COMMIT` | Aprobada |
| Reversión de traslado | `ROLLBACK` revirtió la actualización y la inserción | Aprobada |
| Dos sesiones independientes sobre la misma afiliación | La sesión B esperó el bloqueo de A | Aprobada |
| Continuación de B después del `COMMIT` de A | `UPDATE` de B devolvió 0 filas afectadas | Aprobada |
| Segundo intento concurrente de afiliación activa | Rechazado por error 1062 | Aprobada |

### 11.4 Evidencia de concurrencia

Se utilizaron dos sesiones independientes del cliente MariaDB, cada una con su propia conexión persistente.

La secuencia observada fue:

1. La sesión A inició una transacción, cerró la afiliación `...15` e insertó la afiliación `...17` como activa, sin confirmar todavía la transacción.
2. La sesión B inició otra transacción e intentó cerrar la misma afiliación `...15`.
3. La operación de B quedó esperando el bloqueo mantenido por A.
4. A ejecutó `COMMIT`.
5. B continuó y su actualización devolvió `0 rows affected`.
6. B intentó insertar la afiliación `...18` como activa para el mismo estudiante.
7. MariaDB rechazó la inserción con error `1062`, por duplicidad de `uq_est01_affiliation_current_student`.
8. B ejecutó `ROLLBACK`.

### 11.5 Estado final comprobado

Para el estudiante experimental `22222222222222222222222222`, la consulta final devolvió:

| ID de afiliación | Estado |
|---|---|
| `00000000000000000000000011` | `historical` |
| `00000000000000000000000012` | `historical` |
| `00000000000000000000000015` | `historical` |
| `00000000000000000000000017` | `active` |

Resultado: una sola afiliación activa y conservación de las afiliaciones históricas.

### 11.6 Alcance y limitaciones

**Comprobado experimentalmente:**

- La columna generada persistente y el índice único permiten mantener una sola afiliación activa por estudiante.
- Se admiten múltiples registros históricos.
- El traslado secuencial puede ejecutarse dentro de una transacción.
- El rollback revierte la operación experimental.
- Dos sesiones independientes quedan protegidas frente a intentos concurrentes de crear dos afiliaciones activas.

**Pendiente de comprobación:**

La tabla experimental no tenía claves foráneas hacia `institutions`, `students` o `campuses`, ni una restricción compuesta que garantizara que el campus destino pertenece a la institución del estudiante.

Por tanto, todavía no se ha demostrado la regla de pertenencia institucional del campus destino.

También queda pendiente verificar estas garantías en el diseño físico y en la operación definitiva de EST-01.

### 11.7 Relación con TD-03

TD-03 permanece sin cambios:

- Conservar el historial de afiliaciones.
- Mantener una sola afiliación activa por estudiante.
- Cerrar la afiliación anterior y crear la nueva dentro de una transacción.
- Validar que el campus destino pertenece a la institución del estudiante.
- Soportar concurrencia y revertir completamente la operación si falla.
- No reasignar automáticamente matrículas, calificaciones ni otros registros históricos.

E06 aporta evidencia experimental para las reglas de historial, unicidad, transacción, rollback y concurrencia. No cierra la validación de pertenencia institucional.

### 11.8 Estado de trazabilidad de TD-03

| Dimensión | Estado |
|---|---|
| Historial de afiliaciones | Comprobado experimentalmente |
| Afiliación activa única | Comprobado experimentalmente |
| Traslado transaccional | Comprobado experimentalmente |
| Rollback | Comprobado experimentalmente |
| Protección ante concurrencia | Comprobado experimentalmente |
| Pertenencia institucional del campus destino | Pendiente |
| Implementación definitiva de EST-01 | No autorizada por esta evidencia |

**Conclusión:** E06 demuestra que MariaDB 10.4.32 soporta el mecanismo experimental de integridad de afiliación activa única y su protección ante concurrencia. La validación de pertenencia institucional y la comprobación en el esquema definitivo siguen pendientes.

# 12. E07 — Verificación experimental de pertenencia institucional mediante claves foráneas compuestas

**Proyecto:** CENICOM ERP  
**Frente:** EST-01 — Módulo Student  
**Tipo de evidencia:** Prueba experimental de estructura e integridad referencial  
**Base utilizada:** `cenicom_aca_year02_isolated_test`  
**Motor:** MariaDB `10.4.32-MariaDB`  
**Estado:** Pruebas estructurales y funcionales aprobadas  
**Alcance:** Verificar que una afiliación estudiantil no pueda relacionar estudiantes, campus e instituciones diferentes.

### 12.1 Objetivo

Demostrar que MariaDB 10.4.32 puede garantizar mediante claves foráneas compuestas que:

1. El estudiante pertenece a la institución declarada en la afiliación.
2. El campus pertenece a esa misma institución.
3. Una afiliación válida entre estudiante y campus de la misma institución puede registrarse.
4. Las combinaciones cruzadas entre instituciones son rechazadas por la base de datos.

Las pruebas se realizaron exclusivamente en `cenicom_aca_year02_isolated_test`.

No se modificó la base principal `cenicom`. No se crearon migraciones Laravel.

### 12.2 Estructura física experimental

Se materializaron cuatro tablas aisladas:

- `est01_e07_institutions`
- `est01_e07_students`
- `est01_e07_campuses`
- `est01_e07_student_campus_affiliations`

La estructura se diseñó para comprobar la integridad institucional mediante claves candidatas y referencias compuestas.

**Instituciones**

`est01_e07_institutions`:

- `id CHAR(26)` como clave primaria.

**Estudiantes**

`est01_e07_students`:

- `id CHAR(26)` como clave primaria.
- `institution_id CHAR(26)` obligatorio.
- Índice único compuesto `(institution_id, id)`.
- Clave foránea de `institution_id` hacia `est01_e07_institutions.id`.

**Campus**

`est01_e07_campuses`:

- `id CHAR(26)` como clave primaria.
- `institution_id CHAR(26)` obligatorio.
- Índice único compuesto `(institution_id, id)`.
- Clave foránea de `institution_id` hacia `est01_e07_institutions.id`.

**Afiliaciones**

`est01_e07_student_campus_affiliations`:

- `id CHAR(26)` como clave primaria.
- `institution_id CHAR(26)` obligatorio.
- `student_id CHAR(26)` obligatorio.
- `campus_id CHAR(26)` obligatorio.
- `status VARCHAR(20)` obligatorio.
- `active_student_id CHAR(26)` como columna generada `STORED`.

La columna generada utiliza la expresión:

```sql
CASE
    WHEN status = 'active' THEN student_id
    ELSE NULL
END
```

Índice único:

```sql
UNIQUE (institution_id, active_student_id)
```

Índices de apoyo:

```sql
INDEX (institution_id, student_id)
INDEX (institution_id, campus_id)
```

Claves foráneas compuestas:

```sql
FOREIGN KEY (institution_id, student_id)
REFERENCES est01_e07_students (institution_id, id)
```

```sql
FOREIGN KEY (institution_id, campus_id)
REFERENCES est01_e07_campuses (institution_id, id)
```

Las cuatro tablas utilizan InnoDB, `utf8mb4` y `utf8mb4_unicode_ci`.

Las relaciones referenciales fueron verificadas en `information_schema`. Las cuatro claves foráneas reportaron `ON UPDATE RESTRICT` y `ON DELETE RESTRICT`.

La columna `active_student_id` fue verificada como `STORED GENERATED`, con tipo `CHAR(26)` y nulabilidad compatible con los registros históricos.

### 12.3 Datos de prueba

Se prepararon dos instituciones independientes:

| Elemento | Identificador | Institución |
|---|---|---|
| Institución A | `00000000000000000000000001` | — |
| Institución B | `00000000000000000000000002` | — |
| Estudiante A | `00000000000000000000000101` | A |
| Estudiante B | `00000000000000000000000102` | B |
| Campus A | `00000000000000000000000201` | A |
| Campus B | `00000000000000000000000202` | B |

Los registros de estudiantes y campus fueron consultados y se comprobó su correspondencia institucional.

### 12.4 E07-01 — Afiliación válida

**Objetivo:** comprobar que un estudiante y un campus pertenecientes a la misma institución pueden asociarse.

SQL ejecutado:

```sql
INSERT INTO est01_e07_student_campus_affiliations (
    id,
    institution_id,
    student_id,
    campus_id,
    status
)
VALUES (
    '00000000000000000000000301',
    '00000000000000000000000001',
    '00000000000000000000000101',
    '00000000000000000000000201',
    'active'
);
```

**Resultado observado:** inserción aceptada.

Consulta posterior:

```sql
SELECT
    id,
    institution_id,
    student_id,
    campus_id,
    status,
    active_student_id
FROM est01_e07_student_campus_affiliations;
```

Resultado:

| Campo | Valor |
|---|---|
| `id` | `00000000000000000000000301` |
| `institution_id` | `00000000000000000000000001` |
| `student_id` | `00000000000000000000000101` |
| `campus_id` | `00000000000000000000000201` |
| `status` | `active` |
| `active_student_id` | `00000000000000000000000101` |

**Estado: PASS.**

Se comprobó que la afiliación válida fue aceptada y que la columna generada materializó el identificador del estudiante.

### 12.5 E07-02 — Rechazo de campus perteneciente a otra institución

**Objetivo:** comprobar que no se puede asociar al estudiante A con el campus B declarando la institución A.

SQL ejecutado:

```sql
INSERT INTO est01_e07_student_campus_affiliations (
    id,
    institution_id,
    student_id,
    campus_id,
    status
)
VALUES (
    '00000000000000000000000302',
    '00000000000000000000000001',
    '00000000000000000000000101',
    '00000000000000000000000202',
    'transferred'
);
```

**Resultado observado:**

```text
#1452 - Cannot add or update a child row:
a foreign key constraint fails
(`cenicom_aca_year02_isolated_test`.`est01_e07_student_campus_affiliations`,
CONSTRAINT `fk_e07_affiliations_campus`
FOREIGN KEY (`institution_id`, `campus_id`)
REFERENCES `est01_e07_campuses`)
```

La restricción que rechazó la operación fue:

```text
fk_e07_affiliations_campus
```

**Estado: PASS.**

La base de datos rechazó la combinación porque el campus indicado no pertenece a la institución declarada en la afiliación.

### 12.6 E07-03 — Rechazo de estudiante perteneciente a otra institución

**Objetivo:** comprobar que no se puede asociar al estudiante B con el campus A declarando la institución A.

SQL ejecutado:

```sql
INSERT INTO est01_e07_student_campus_affiliations (
    id,
    institution_id,
    student_id,
    campus_id,
    status
)
VALUES (
    '00000000000000000000000303',
    '00000000000000000000000001',
    '00000000000000000000000102',
    '00000000000000000000000201',
    'transferred'
);
```

**Resultado observado:**

```text
#1452 - Cannot add or update a child row:
a foreign key constraint fails
(`cenicom_aca_year02_isolated_test`.`est01_e07_student_campus_affiliations`,
CONSTRAINT `fk_e07_affiliations_student`
FOREIGN KEY (`institution_id`, `student_id`)
REFERENCES `est01_e07_students`)
```

La restricción que rechazó la operación fue:

```text
fk_e07_affiliations_student
```

**Estado: PASS.**

La base de datos rechazó la combinación porque el estudiante indicado no pertenece a la institución declarada en la afiliación.

### 12.7 Matriz de resultados E07

| Prueba | Escenario | Resultado de MariaDB | Estado |
|---|---|---|---|
| E07-01 | Estudiante A + campus A + institución A | Inserción aceptada | Aprobada |
| E07-02 | Estudiante A + campus B + institución A | Error `#1452`, `fk_e07_affiliations_campus` | Aprobada |
| E07-03 | Estudiante B + campus A + institución A | Error `#1452`, `fk_e07_affiliations_student` | Aprobada |

En las pruebas negativas se utilizó el estado `transferred` para evitar que el índice de afiliación activa única interfiriera con la comprobación de las claves foráneas.

### 12.8 Conclusiones técnicas

E07 demuestra experimentalmente que MariaDB 10.4.32 soporta el diseño de integridad referencial requerido para impedir afiliaciones entre instituciones diferentes.

Se comprobó que:

1. Las claves candidatas `(institution_id, id)` de estudiantes y campus pueden utilizarse como destino de claves foráneas compuestas.
2. La FK compuesta de estudiante rechaza estudiantes asociados a una institución diferente.
3. La FK compuesta de campus rechaza campus asociados a una institución diferente.
4. Una afiliación válida entre estudiante y campus de la misma institución es aceptada.
5. La columna generada `active_student_id` materializa correctamente el identificador del estudiante en una afiliación activa.
6. Las restricciones referenciales se encuentran materializadas en MariaDB y no dependen exclusivamente de validaciones de aplicación.

### 12.9 Relación con TD-03

TD-03 permanece sin cambios.

E07 aporta evidencia para la regla de pertenencia institucional del estudiante y del campus, complementando la evidencia experimental E06 sobre historial, unicidad de afiliación activa, transacciones, rollback y concurrencia.

La combinación de E06 y E07 cubre experimentalmente las principales garantías técnicas de afiliación:

- Historial de afiliaciones.
- Una sola afiliación activa por estudiante.
- Traslado transaccional.
- Reversión completa ante fallos.
- Protección ante concurrencia.
- Correspondencia institucional entre estudiante y campus.

Estas pruebas experimentales no sustituyen las pruebas de integración que deberán realizarse cuando exista la implementación definitiva.

### 12.10 Límites de E07

- Las pruebas se ejecutaron exclusivamente en `cenicom_aca_year02_isolated_test`.
- Las cuatro tablas `est01_e07_*` y sus registros de prueba se conservan.
- No se ejecutaron operaciones de limpieza ni eliminación.
- No se modificó la base principal `cenicom`.
- No se crearon ni ejecutaron migraciones Laravel para EST-01.
- La prueba no demuestra por sí sola la implementación de traslados en el dominio ni su integración con matrículas, calificaciones o auditoría.
- El diseño experimental debe trasladarse al contrato físico definitivo mediante una revisión explícita de impacto antes de autorizar implementación.

### 12.11 Estado de trazabilidad de TD-03

| Dimensión | Evidencia | Estado |
|---|---|---|
| Historial de afiliaciones | E06 | Comprobado experimentalmente |
| Afiliación activa única | E06 y E07 | Comprobado experimentalmente |
| Traslado transaccional | E06 | Comprobado experimentalmente |
| Rollback | E06 | Comprobado experimentalmente |
| Protección ante concurrencia | E06 | Comprobado experimentalmente |
| Pertenencia institucional del estudiante | E07-03 | Comprobado experimentalmente |
| Pertenencia institucional del campus | E07-02 | Comprobado experimentalmente |
| Afiliación válida dentro de una institución | E07-01 | Comprobado experimentalmente |
| Implementación definitiva de EST-01 | — | No autorizada por esta evidencia |

### 12.12 Estado final de E07

**E07: CERRADA — EVIDENCIA ESTRUCTURAL Y FUNCIONAL APROBADA.**

La evidencia confirma que el diseño de claves foráneas compuestas es técnicamente viable en MariaDB 10.4.32 para garantizar la correspondencia institucional entre estudiantes, campus y afiliaciones.

**Conclusión general:** E06 y E07 proporcionan evidencia experimental complementaria para el contrato de afiliaciones de EST-01. El siguiente paso consiste en revisar el impacto de trasladar el diseño aprobado al contrato físico definitivo de Student, sin modificar la base principal, sin crear migraciones y sin reabrir ACA-YEAR-02.
