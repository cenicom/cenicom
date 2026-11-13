Matriz funcional de roles y permisos — Fase 5
Vamos a consolidar la matriz como contrato funcional, antes de crear permisos técnicos, módulos o migraciones.

La matriz aplica a los seis módulos: Estudiantes, Docentes, Cursos, Asignaturas, Grados y Matrículas. Cada operación será independiente: consultar no autoriza a modificar; modificar no autoriza a eliminar; y ninguna de estas operaciones concede aprobación automáticamente.
1. Roles y alcance
Rol	Alcance autorizado
Administrador de plataforma (AP)	Administración técnica; sin acceso académico indiscriminado
Administrador institucional (AI)	Institución y sus sedes
Rector (REC)	Institución y todas sus sedes
Vicerrector (VREC)	Institución y todas sus sedes
Coordinador académico (AC)	Institución y todas sus sedes
Secretario académico (AS)	Institución y funciones asignadas
Encargado de sede (ES)	Exclusivamente su sede asignada
Docente (DOC)	Estudiantes, cursos y asignaturas vinculados a sus asignaciones
Estudiante (EST)	Su propia información
Acudiente (ACU)	Información de sus representados vinculados
Auditor (AUD)	Solo lectura de los datos expresamente autorizados

En todas las operaciones aplicables se validará también el año lectivo, la asignación docente, la relación acudiente-estudiante, el estado del registro y la autoridad requerida para aprobar.
2. Matriz por módulo y operación
Leyenda: ✓ permitido dentro del alcance del rol; D solo mediante delegación expresa; — no autorizado por defecto; C condicionado a que no existan registros dependientes.
Módulo	Consultar	Crear	Modificar	Eliminar	No Activo	Aprobar
Estudiantes	Roles según alcance	AI, REC, VREC, AC, AS*, ES	AI, REC, VREC, AC, AS*, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)
Docentes	Roles según alcance	AI, REC, VREC, AC, ES	AI, REC, VREC, AC, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)
Cursos	Roles según alcance	AI, REC, VREC, AC, ES	AI, REC, VREC, AC, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)
Asignaturas	Roles según alcance	AI, REC, VREC, AC, ES	AI, REC, VREC, AC, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)
Grados	Roles según alcance	AI, REC, VREC, AC, ES	AI, REC, VREC, AC, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)
Matrículas	Roles según alcance	AI, REC, VREC, AS, ES	AI, REC, VREC, AS, ES	AI, REC, VREC (C)	Roles con autorización de gestión	REC, VREC; AC (D)

* AS: puede modificar datos generales del estudiante y ejecutar las funciones asignadas. La creación de estudiantes por parte de AS queda contemplada según la matriz anterior; sus funciones adicionales deben quedar delimitadas por asignación expresa.

Precisiones importantes:
- AI conserva facultades de gestión dentro de la institución, pero no obtiene aprobación final por defecto.
- ES puede crear y modificar los seis tipos de registros únicamente en su sede.
- AC puede crear y modificar estudiantes, docentes, cursos, asignaturas y grados; sus facultades sobre matrículas deben derivarse de funciones asignadas o delegadas.
- DOC puede consultar estudiantes vinculados a sus cursos y asignaturas, pero no administrar matrículas ni registros institucionales generales. La modificación se limita a información académica propia de su función.
- EST y ACU tienen permisos de consulta restringidos a su propia información y a la de sus representados, respectivamente.
- AUD solo puede consultar información expresamente autorizada.
- AP no recibe permisos académicos por su rol técnico.
La tabla es la propuesta consolidada; las autorizaciones condicionadas no deben interpretarse como permisos automáticos.
3. Reglas obligatorias de eliminación y aprobación
Eliminación
1. Solo AI, REC y VREC pueden tener el permiso de eliminación.
2. Si existen matrículas, asignaciones, calificaciones u otros registros dependientes, la eliminación se bloquea.
3. Cuando corresponda, se permite cambiar el registro a No Activo, preservando su historial y relaciones.
4. El cambio a No Activo requiere un permiso propio; no se deriva de modificar.
5. La desactivación no debe borrar ni alterar retrospectivamente los registros académicos.
Aprobación
1. REC y VREC pueden aprobar dentro de sus atribuciones.
2. AC puede aprobar únicamente procesos delegados de forma expresa.
3. AS puede preparar y gestionar matrículas, pero no adquiere aprobación final automáticamente.
4. ES, DOC, EST, ACU y AUD no aprueban por defecto.
5. Cada aprobación debe comprobar que el proceso está en un estado que admite esa transición.
4. Permisos técnicos que se derivan
Para cada módulo se contemplan, según corresponda, permisos independientes con nombres como:
Permiso	Propósito
students.view	Consultar estudiantes autorizados
students.create	Crear estudiantes
students.update	Modificar estudiantes
students.delete	Eliminar si cumple las restricciones
students.deactivate	Cambiar a No Activo
students.approve	Aprobar procesos del módulo, si aplica

El mismo patrón se adaptará a docentes, cursos, asignaturas, grados y matrículas. Los nombres son una propuesta de catálogo, no permisos ya registrados en CENICOM. Las consultas de docentes, estudiantes y acudientes también necesitarán restricciones por relación y alcance; un permiso genérico no basta.
5. Pendientes para cerrar el contrato sin ambigüedades
Hay tres puntos que todavía requieren definición expresa:
1. Vicerrector y Administrador institucional: confirmar si pueden crear y modificar docentes, cursos, asignaturas, grados y matrículas en toda la institución. La matriz actual los contempla.
2. No Activo: definir qué roles pueden ejecutar esta transición en cada módulo. No conviene concederla automáticamente a todos los que pueden modificar.
3. Aprobación: precisar en qué procesos se exige aprobación formal. La matriz contempla la aprobación para los procesos aplicables, pero no todos los registros requieren necesariamente una.
Mi recomendación es resolver estos tres puntos y declarar la matriz cerrada. Después pasamos al catálogo definitivo de permisos, sus asignaciones por rol y las reglas de alcance, todavía sin implementar código hasta que lo autorices.
