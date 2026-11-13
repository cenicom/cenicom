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
*//*-/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/-*/*-/-*/-*/-*/-*/-*/-*/*/
1. Roles propuestos
Esta es una propuesta inicial para validar, no una configuración que ya exista en el sistema.
Rol	Responsabilidad principal
Administrador de plataforma	Administrar la plataforma y las instituciones autorizadas.
Administrador de institución	Gestionar la operación académica de su institución.
Rector / Director	Supervisar y autorizar operaciones institucionales.
Coordinador académico	Gestionar la organización y el seguimiento académico.
Secretario académico	Administrar registros y procesos de matrícula.
Docente	Consultar sus asignaciones y registrar información académica autorizada.
Estudiante	Consultar su propia información académica.
Representante / Acudiente	Consultar la información autorizada de sus representados.
Auditor	Consultar información y trazabilidad sin modificar registros.

Los roles no deben conceder acceso automático a todas las instituciones o sedes. El alcance se controla por separado.
2. Matriz funcional inicial
Leyenda: A = administrar; G = gestionar; R = consultar; P = acceso propio o asignado; — = sin acceso por defecto.
Módulo	Admin. institución	Rector	Coord. académico	Secretario	Docente	Estudiante	Acudiente	Auditor
Estudiantes	A	R	G	G	P	P	P	R
Docentes	A	R	G	R	P	—	—	R
Cursos	A	R	G	R	P	P	P	R
Asignaturas	A	R	G	R	P	P	P	R
Grados	A	R	G	R	P	P	P	R
Matrículas	A	G	R	G	—	P	P	R

El administrador de plataforma queda fuera de esta tabla porque sus funciones son transversales y no deben implicar acceso indiscriminado a los datos académicos.
3. Reglas que debe respetar la autorización
1. Permiso funcional: qué operación puede realizar la persona.
2. Institución: a qué institución puede acceder.
3. Sede: en qué campus puede operar, cuando corresponda.
4. Año lectivo: sobre qué periodo académico puede actuar.
5. Relación con el recurso: por ejemplo, docente asignado al curso, estudiante titular o acudiente vinculado al estudiante.
Además, consultar no equivale a modificar; y modificar no equivale a autorizar o cerrar un proceso.
4. Primera decisión del contrato
Antes de convertir esta propuesta en una matriz definitiva, debemos resolver una regla que afecta a todos los roles:

¿El rector, el coordinador académico y el secretario académico tendrán alcance sobre toda la institución o solo sobre las sedes que tengan asignadas?

Mi recomendación es que el alcance sea explícito y configurable: toda la institución cuando exista autorización institucional expresa; en caso contrario, únicamente las sedes asignadas. Esto evita conceder acceso por el solo nombre del cargo.

/*-/-*/*/*-/*-/-*/*-/-*/-*/*-/-*/*-/-*/-*/-*/-*/-*/*-/*-/-*/-*/-*/-*/-*/-*/-*/*-
1. Matriz de permisos propuesta para cierre
Abreviaturas: AI = Administrador institucional; REC = Rector; VREC = Vicerrector; AC = Coordinador académico; AS = Secretario académico; ES = Encargado de sede; DOC = Docente; EST = Estudiante; ACU = Acudiente; AUD = Auditor; AP = Administrador de plataforma.
Operación	AI	REC	VREC	AC	AS	ES	DOC	EST / ACU	AUD
Consultar estudiantes	Institución	Institución	Institución	Institución	Institución	Sede asignada	Solo estudiantes vinculados a sus asignaciones	Solo información propia / de representados	Solo alcance autorizado
Crear estudiantes	Institución	Institución	Institución	Institución	Institución	Sede asignada	No	No	No
Modificar estudiantes	Institución	Institución	Institución	Institución	Institución	Sede asignada	Solo información académica de su función	No	No
Eliminar estudiantes	Institución	Sí	Sí	No	No	No	No	No	No
Consultar docentes	Institución	Institución	Institución	Institución	Institución	Sede asignada	Solo información necesaria para su función	No	Solo alcance autorizado
Crear / modificar docentes	Institución	Institución	Institución	Institución	Institución	Sede asignada	No	No	No
Eliminar docentes	Institución	Sí	Sí	No	No	No	No	No	No
Gestionar cursos, asignaturas y grados	Institución	Institución	Institución	Institución	Institución	Sede asignada	Solo consulta de los propios asignados	No	No
Gestionar matrículas	Institución	Institución	Institución	Institución	Institución	Sede asignada	No	No	No
Aprobar procesos académicos	Según delegación autorizada	Sí	Sí	Solo si está delegado	No por defecto	No por defecto	No por defecto	No	No
Consultar información académica	Institución	Institución	Institución	Institución	Institución	Sede asignada	Solo sus asignaciones	Solo propia / representados	Solo alcance autorizado

Reglas transversales:
- El AP no obtiene acceso académico indiscriminado por ser administrador técnico.
- Ningún rol obtiene acceso automático a todas las instituciones o sedes.
- La eliminación se reserva a AI, REC y VREC, siempre dentro de su alcance institucional.
- El docente no administra matrículas ni datos institucionales generales.
- El auditor tiene acceso exclusivamente de lectura.
- Consultar, crear, modificar, eliminar y aprobar son permisos diferentes.
- Las restricciones deben validarse en el servidor, no solamente ocultando opciones en pantalla.
- La autorización debe considerar el año lectivo cuando la operación corresponda a ese contexto.
2. Alcance de cada módulo
Módulo	Operaciones que debe contemplar
Estudiantes	Consultar, crear, modificar, eliminar y consultar información académica propia o autorizada
Docentes	Consultar, crear, modificar y eliminar
Cursos	Consultar, crear, modificar y eliminar
Asignaturas	Consultar, crear, modificar y eliminar
Grados	Consultar, crear, modificar y eliminar
Matrículas	Consultar, crear, modificar, eliminar según autorización y aprobar mediante autoridad competente

La matriz define autorizaciones funcionales; no implica que ya existan los módulos, las migraciones o las políticas técnicas. Tampoco concede automáticamente a los roles institucionales todas las operaciones enumeradas: el alcance indicado en cada celda es obligatorio.
3. Puntos que debemos cerrar antes de declarar la matriz definitiva
Hay cuatro decisiones que no conviene inventar:
1. Eliminación: ¿se permitirá eliminar estudiantes, docentes, cursos, asignaturas, grados y matrículas en cualquier estado, o se restringirá cuando existan matrículas, calificaciones u otras dependencias? Recomiendo bloquear la eliminación cuando haya dependencias y definir una baja lógica cuando corresponda.
2. Encargado de sede: ¿puede crear y modificar docentes, estudiantes, cursos, asignaturas, grados y matrículas únicamente de su sede? La propuesta actual dice que sí, dentro de las operaciones autorizadas.
3. Coordinador académico: ¿puede crear y modificar estudiantes, docentes, cursos, asignaturas y grados, o su gestión debe limitarse a la organización académica y a las aprobaciones delegadas?
4. Secretario académico: ¿puede modificar datos generales del estudiante o solo los datos administrativos y académicos relacionados con matrícula y expediente?
Mi recomendación es resolver estos cuatro puntos en ese orden. Con esas respuestas consolidamos la matriz como contrato cerrado y después derivamos el catálogo técnico de permisos, sin escribir código todavía.
