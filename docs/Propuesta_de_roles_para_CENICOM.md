3. Propuesta de roles para CENICOM
Recomiendo un modelo RBAC: los permisos describen acciones concretas y los roles agrupan esos permisos. El rol por sí solo no debe conceder acceso ilimitado a todas las instituciones, sedes o estudiantes.

Esta sería una propuesta funcional inicial, todavía no un catálogo aprobado ni una descripción de lo que ya está implementado.
Rol propuesto	Responsabilidad principal
Administrador de plataforma	Configuración global y administración técnica autorizada de CENICOM.
Administrador de institución	Gestionar la institución, sus sedes, su configuración académica y sus usuarios autorizados.
Rector / Director	Supervisar la actividad académica de su institución y autorizar operaciones según sus atribuciones.
Coordinador académico	Gestionar la planificación académica, períodos, cursos, asignaturas y calificaciones según el alcance autorizado.
Secretario académico	Administrar expedientes, estudiantes y matrículas, y mantener los datos académicos autorizados.
Docente	Consultar sus asignaciones y gestionar la información académica de los grupos y estudiantes que tiene asignados.
Estudiante	Consultar su propia información académica y las calificaciones que tenga derecho a ver.
Representante / acudiente	Consultar únicamente la información de los estudiantes vinculados y autorizados.
Auditor / consulta	Consultar información y trazabilidad sin modificar los registros.

No es necesario crear nueve roles técnicos desde el primer día. Podemos aprobar los perfiles funcionales y activar únicamente los necesarios para cada módulo. Por ejemplo, rector y coordinador podrían compartir algunos permisos sin ser necesariamente el mismo rol.
4. Catálogo de permisos propuesto
Conviene mantener una nomenclatura uniforme, compatible con el patrón existente de CENICOM (institutions.*, campuses.* y academic_year.*).
A. Administración académica — Fase 4

Año lectivo, períodos y calendario escolar.
- academic_year.view
- academic_year.create
- academic_year.update
- academic_year.activate
- academic_year.cancel
- academic_year.close
- academic_period.view
- academic_period.create
- academic_period.update
- academic_period.close
- school_calendar.view
- school_calendar.manage


B. Estudiantes

Expedientes e información personal y académica.
- students.view
- students.create
- students.update
- students.deactivate
- students.view_sensitive


C. Docentes
- teachers.view
- teachers.create
- teachers.update
- teachers.deactivate
- teachers.assign


D. Estructura académica
- courses.view, courses.create, courses.update
- subjects.view, subjects.create, subjects.update
- grades.view, grades.enter, grades.update, grades.publish


E. Matrículas
- enrollments.view
- enrollments.create
- enrollments.update
- enrollments.approve
- enrollments.cancel


Estos nombres son una propuesta inicial. Antes de aprobarlos, debemos contrastarlos con las operaciones reales de cada módulo y con el catálogo y cargador de permisos que ya existen. Por ejemplo, no introduciría grades.publish hasta definir quién puede publicar notas y qué significa publicar en el contrato académico.
5. Reglas de seguridad que recomiendo aprobar
Los permisos no bastan si no se limita el alcance de los datos. Propongo definir estas reglas antes de desarrollar Fase 5:
1. Alcance institucional: un usuario solo puede operar en las instituciones para las que está autorizado.
2. Alcance por sede: si el rol está limitado a una sede, no puede administrar automáticamente las demás.
3. Alcance por año lectivo: las operaciones deben respetar el estado del año lectivo y las restricciones del contrato académico.
4. Docentes asignados: un docente no obtiene acceso a todos los estudiantes por tener el permiso de consultar notas.
5. Estudiantes y representantes: cada uno consulta únicamente los expedientes que le corresponden.
6. Auditoría: las operaciones relevantes registran actor, acción, resultado y contexto necesario, sin registrar datos sensibles innecesarios.
7. Sin borrados indiscriminados: bajas, cancelaciones y cierres deben utilizar los flujos definidos para cada entidad; no conceder borrado físico general por defecto.
8. Separación de funciones: cuando el proceso lo requiera, la persona que registra una operación no debería poder aprobarla automáticamente por el solo hecho de tener un rol amplio.
6. Orden de trabajo de la Fase 5
Mantendría el orden ya previsto:
1

Student — Estudiantes

Identidad académica, expediente y protección de datos.


2

Teacher — Docentes

Expediente, estado y asignaciones.


3

Course — Cursos

Organización de grupos por institución, sede y año lectivo.


4

Subject — Asignaturas

Catálogo y relación con los cursos.


5

Grade — Calificaciones

Evaluación, registro, modificación y publicación autorizada.


6

Enrollment — Matrículas

Vinculación del estudiante con el año lectivo, la sede y el curso.



El orden es una propuesta de secuencia, no autorización para empezar a programar sin contratos. Las dependencias entre curso, asignatura, calificaciones y matrícula deben quedar definidas antes de implementar cada entidad.
7. Cómo propongo proceder ahora
Mi recomendación es trabajar en dos frentes controlados:
- Frente A — Cierre de Fase 4: completar las brechas verificadas en la matriz y declarar el cierre.
- Frente B — Preparación de Fase 5: elaborar el contrato de roles, permisos y alcance de acceso; después definir el contrato de Student.
Para evitar diseñar un sistema de permisos paralelo al existente, el siguiente paso técnico debería ser revisar el catálogo actual de permisos y su mecanismo de asignación a roles, no crear archivos ni migraciones todavía.

La decisión que necesitamos tomar es esta: ¿aprobamos esta propuesta como base de trabajo para el contrato de roles y permisos, sujeta a verificar el catálogo actual, sin autorizar aún la implementación de Fase 5?
