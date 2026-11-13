Catálogo contractual de permisos — Fase 5 de CENICOM
Estado: propuesta consolidada para aprobación. Este catálogo define los permisos funcionales para Estudiantes, Docentes, Cursos, Asignaturas, Grados y Matrículas. No crea módulos, migraciones, registros de base de datos ni código.
1. Convenciones
Código	Rol
AP	Administrador de plataforma
AI	Administrador institucional
REC	Rector
VREC	Vicerrector
AC	Coordinador académico
AS	Secretario académico
ES	Encargado de sede
DOC	Docente
EST	Estudiante
ACU	Acudiente
AUD	Auditor

Los nombres de permisos siguen el formato recurso.operacion. Para las seis entidades, el catálogo distingue consulta, creación, modificación, eliminación, desactivación y aprobación cuando la operación aplica.

Reglas de alcance: AI y VREC solo consultan la información de la institución y sus sedes; ES opera únicamente en su sede; DOC queda limitado a sus asignaciones; EST consulta su propia información; ACU consulta la de sus representados; AUD tiene acceso de solo lectura a los datos expresamente autorizados; AP no obtiene acceso académico indiscriminado.
2. Catálogo de permisos por módulo
Los roles enumerados son los autorizados funcionalmente, siempre dentro de su alcance. Las restricciones de estado, dependencias y asignaciones se aplican adicionalmente.
A. Estudiantes
Permiso	Descripción	Roles autorizados
students.view	Consultar estudiantes e información autorizada	REC, VREC, AC, AS, ES, DOC, EST, ACU, AUD
students.create	Registrar estudiantes	REC, AC, AS, ES
students.update	Modificar datos generales o información permitida	REC, AC, AS, ES
students.delete	Eliminar si no hay registros dependientes	REC
students.deactivate	Cambiar a No Activo conservando el historial	AS
students.approve	Aprobar procesos de estudiantes cuando corresponda	REC, VREC, AC según atribución

B. Docentes
Permiso	Descripción	Roles autorizados
teachers.view	Consultar docentes autorizados	REC, VREC, AC, AS, ES, AUD
teachers.create	Registrar docentes	REC, AC, ES
teachers.update	Modificar datos de docentes	REC, AC, ES
teachers.delete	Eliminar sin registros dependientes	REC
teachers.deactivate	Cambiar a No Activo conservando el historial	AS
teachers.approve	Aprobar procesos de docentes cuando corresponda	REC, VREC, AC según atribución

C. Cursos
Permiso	Descripción	Roles autorizados
courses.view	Consultar cursos autorizados	REC, VREC, AC, AS, ES, DOC, AUD
courses.create	Crear cursos	REC, AC, ES
courses.update	Modificar cursos	REC, AC, ES
courses.delete	Eliminar cursos sin registros dependientes	REC
courses.deactivate	Cambiar a No Activo conservando el historial	AS
courses.approve	Aprobar procesos de cursos cuando corresponda	REC, VREC, AC según atribución

D. Asignaturas
Permiso	Descripción	Roles autorizados
subjects.view	Consultar asignaturas autorizadas	REC, VREC, AC, AS, ES, DOC, AUD
subjects.create	Crear asignaturas	REC, AC, ES
subjects.update	Modificar asignaturas	REC, AC, ES
subjects.delete	Eliminar asignaturas sin registros dependientes	REC
subjects.deactivate	Cambiar a No Activo conservando el historial	AS
subjects.approve	Aprobar procesos de asignaturas cuando corresponda	REC, VREC, AC según atribución

E. Grados
Permiso	Descripción	Roles autorizados
grades.view	Consultar grados autorizados	REC, VREC, AC, AS, ES, DOC, AUD
grades.create	Crear grados	REC, AC, ES
grades.update	Modificar grados	REC, AC, ES
grades.delete	Eliminar grados sin registros dependientes	REC
grades.deactivate	Cambiar a No Activo conservando el historial	AS
grades.approve	Aprobar procesos de grados cuando corresponda	REC, VREC, AC según atribución

F. Matrículas
Permiso	Descripción	Roles autorizados
enrollments.view	Consultar matrículas dentro del alcance autorizado	REC, VREC, AC, AS, ES, AUD
enrollments.create	Registrar matrículas	REC, AS, ES
enrollments.update	Modificar matrículas según las funciones asignadas	REC, AS, ES
enrollments.delete	Eliminar matrículas sin registros dependientes	REC
enrollments.deactivate	Cambiar a No Activo conservando el historial	AS
enrollments.approve	Aprobar procesos de matrícula cuando corresponda	REC, VREC, AC según atribución

3. Restricciones que forman parte del contrato
Estas reglas limitan todos los permisos anteriores:
- AI: consulta información de la institución y sus sedes; no recibe permisos de gestión de los seis módulos.
- VREC: consulta los registros permitidos y puede aprobar procesos cuya autoridad le corresponda. No recibe permisos generales de creación, modificación, eliminación ni desactivación.
- REC: gestiona dentro de sus atribuciones institucionales; la aprobación depende del proceso.
- AC: crea y modifica estudiantes, docentes, cursos, asignaturas y grados; aprueba solo procesos que le correspondan.
- AS: modifica datos generales de estudiantes y ejecuta funciones asignadas, incluida la gestión administrativa de matrículas. Es el único rol autorizado para cambiar los registros de estos módulos a No Activo.
- ES: gestiona exclusivamente registros de su sede.
- DOC: consulta estudiantes, cursos y asignaturas relacionados con sus asignaciones y modifica únicamente información académica de su función.
- EST y ACU: solo consultan información propia o de representados vinculados; no tienen permisos de escritura.
- AUD: consulta exclusivamente los datos autorizados; no modifica ni aprueba.
- AP: no recibe permisos académicos automáticamente.
La eliminación se bloquea cuando existen registros dependientes, asignaciones o relaciones que deban conservarse. Un permiso de modificación no concede permiso de eliminación ni de aprobación. Las operaciones deben validarse en el servidor y considerar el año lectivo cuando corresponda.
4. Dos precisiones para no convertir supuestos en reglas
Antes de declarar este catálogo completamente cerrado, quedan por confirmar dos detalles:
1. Cambio a No Activo: he aplicado el permiso de Secretaría a los seis módulos, interpretando tu regla como general. Si Secretaría solo puede desactivar determinados tipos de registro, debemos especificarlos.
2. Aprobación: los seis permisos .approve son nombres propuestos para procesos que realmente necesiten aprobación formal. La autoridad se determinará por proceso; no significa que cada alta o modificación requiera aprobación.
Además, students.view para EST y ACU representa consultas restringidas a sus propios datos o representados, no acceso general a estudiantes.

Con estas precisiones resueltas, el catálogo podrá declararse cerrado como documento contractual. No se ha implementado ningún permiso ni modificado código.
/*-/*-/-*/-*/-*/-*/-*/-*/*-/*-/*-/*-/-*/-*/*-/-*/-*/-*/-*/-*/-*/-*/-*/*-/-*/-*/-*/*-/
Especificación técnica de autorización — Fase 5 de CENICOM
Estado: documento de diseño; implementación no autorizada.

El propósito de esta etapa es convertir el contrato funcional aprobado en reglas técnicas verificables, sin crear todavía módulos, migraciones, modelos, políticas ni permisos en la base de datos.

La especificación debe separar cuatro elementos:
1. Permiso: qué operación puede ejecutar el usuario.
2. Recurso: sobre qué entidad y registro se ejecuta.
3. Alcance: en qué institución, sede, año lectivo o relación puede actuar.
4. Regla de autorización: qué condiciones debe cumplir la solicitud para permitirse.
1. Convenciones técnicas
Elemento	Convención
Módulos	students, teachers, courses, subjects, grades, enrollments
Operaciones	view, create, update, delete, deactivate, approve
Identidad	Usuario autenticado, roles y permisos efectivos
Alcance institucional	Institución propietaria del recurso
Alcance por sede	Sede propietaria o asignada al recurso
Contexto académico	Año lectivo aplicable a la operación
Autorización	Comprobación en servidor, antes de ejecutar la acción

Los nombres de módulos y permisos son identificadores propuestos para la futura implementación; no afirman que esos módulos existan actualmente.
2. Catálogo técnico de permisos
Se propone el mismo conjunto de operaciones para cada módulo, con approve habilitado solo cuando el proceso tenga aprobación formal.
Módulo	Recurso técnico	Permisos propuestos
Estudiantes	Student	students.view, students.create, students.update, students.delete, students.deactivate, students.approve
Docentes	Teacher	teachers.view, teachers.create, teachers.update, teachers.delete, teachers.deactivate, teachers.approve
Cursos	Course	courses.view, courses.create, courses.update, courses.delete, courses.deactivate, courses.approve
Asignaturas	Subject	subjects.view, subjects.create, subjects.update, subjects.delete, subjects.deactivate, subjects.approve
Grados	Grade	grades.view, grades.create, grades.update, grades.delete, grades.deactivate, grades.approve
Matrículas	Enrollment	enrollments.view, enrollments.create, enrollments.update, enrollments.delete, enrollments.deactivate, enrollments.approve

Los 36 nombres representan el catálogo propuesto. La existencia de un nombre en este documento no implica que deba registrarse automáticamente: las operaciones no aplicables a un proceso concreto deben permanecer sin uso.
3. Reglas de autorización por operación
Permiso	Condiciones técnicas obligatorias
*.view	Permiso de consulta y comprobación de alcance antes de devolver datos.
*.create	Permiso de creación, institución y sede válidas, y año lectivo cuando corresponda.
*.update	Permiso de modificación, recurso dentro del alcance y transición de estado permitida.
*.delete	Permiso explícito, ausencia de registros dependientes y validación de integridad.
*.deactivate	Permiso específico de Secretaría, estado compatible y conservación del historial.
*.approve	Permiso explícito, autoridad competente para el proceso y estado que admita aprobación.

La autorización no debe depender únicamente de la presencia de un rol. Debe comprobar también el permiso, el recurso y sus relaciones.
4. Matriz técnica de alcance por rol
Rol	Alcance y regla de aplicación
AP	Sin permisos académicos implícitos.
AI	Consulta de información institucional y sedes; no gestión general de los seis módulos.
REC	Gestión institucional dentro de sus atribuciones; aprobación según proceso.
VREC	Consulta institucional y de sedes; aprobación de procesos que le correspondan.
AC	Crear y modificar estudiantes, docentes, cursos, asignaturas y grados; aprobar según proceso.
AS	Modificar datos generales de estudiantes y gestionar funciones asignadas; cambio a No Activo en los seis módulos.
ES	Crear y modificar registros de los seis módulos, exclusivamente en su sede asignada.
DOC	Consulta de estudiantes, cursos y asignaturas vinculados a sus asignaciones; modificación solo de información académica propia de su función.
EST	Consulta de su propia información.
ACU	Consulta de información de sus representados vinculados.
AUD	Solo lectura de datos expresamente autorizados.

Para cada solicitud, el servidor deberá validar las relaciones pertinentes:
- institution_id del recurso y la institución autorizada.
- campus_id del recurso y la sede autorizada.
- Año lectivo y estado del recurso, cuando apliquen.
- Asignaciones del docente.
- Identidad del estudiante o vínculo de representación del acudiente.
- Autoridad de aprobación y estado del proceso.
Estos nombres de campos son referencias técnicas para el diseño; la estructura real deberá definirse al diseñar cada módulo.
5. Flujo técnico previsto
#chatgpt-mermaid-_r_267_{font-family:-apple-system-body,ui-sans-serif,-apple-system,system-ui,"Segoe UI",Helvetica,"Apple Color Emoji",Arial,sans-serif,"Segoe UI Emoji","Segoe UI Symbol";font-size:16px;fill:rgb(237, 237, 237);}@keyframes edge-animation-frame{from{stroke-dashoffset:0;}}@keyframes dash{to{stroke-dashoffset:0;}}#chatgpt-mermaid-_r_267_ .edge-animation-slow{stroke-dasharray:9,5!important;stroke-dashoffset:900;animation:dash 50s linear infinite;stroke-linecap:round;}#chatgpt-mermaid-_r_267_ .edge-animation-fast{stroke-dasharray:9,5!important;stroke-dashoffset:900;animation:dash 20s linear infinite;stroke-linecap:round;}#chatgpt-mermaid-_r_267_ .error-icon{fill:rgb(48, 48, 48);}#chatgpt-mermaid-_r_267_ .error-text{fill:rgb(237, 237, 237);stroke:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .edge-thickness-normal{stroke-width:1px;}#chatgpt-mermaid-_r_267_ .edge-thickness-thick{stroke-width:3.5px;}#chatgpt-mermaid-_r_267_ .edge-pattern-solid{stroke-dasharray:0;}#chatgpt-mermaid-_r_267_ .edge-thickness-invisible{stroke-width:0;fill:none;}#chatgpt-mermaid-_r_267_ .edge-pattern-dashed{stroke-dasharray:3;}#chatgpt-mermaid-_r_267_ .edge-pattern-dotted{stroke-dasharray:2;}#chatgpt-mermaid-_r_267_ .marker{fill:rgb(175, 175, 175);stroke:rgb(175, 175, 175);}#chatgpt-mermaid-_r_267_ .marker.cross{stroke:rgb(175, 175, 175);}#chatgpt-mermaid-_r_267_ svg{font-family:-apple-system-body,ui-sans-serif,-apple-system,system-ui,"Segoe UI",Helvetica,"Apple Color Emoji",Arial,sans-serif,"Segoe UI Emoji","Segoe UI Symbol";font-size:16px;}#chatgpt-mermaid-_r_267_ p{margin:0;}#chatgpt-mermaid-_r_267_ .label{font-family:-apple-system-body,ui-sans-serif,-apple-system,system-ui,"Segoe UI",Helvetica,"Apple Color Emoji",Arial,sans-serif,"Segoe UI Emoji","Segoe UI Symbol";color:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .cluster-label text{fill:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .cluster-label span{color:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .cluster-label span p{background-color:transparent;}#chatgpt-mermaid-_r_267_ .label text,#chatgpt-mermaid-_r_267_ span{fill:rgb(237, 237, 237);color:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .node rect,#chatgpt-mermaid-_r_267_ .node circle,#chatgpt-mermaid-_r_267_ .node ellipse,#chatgpt-mermaid-_r_267_ .node polygon,#chatgpt-mermaid-_r_267_ .node path{fill:rgb(9, 23, 44);stroke:rgb(31, 78, 148);stroke-width:1px;}#chatgpt-mermaid-_r_267_ .rough-node .label text,#chatgpt-mermaid-_r_267_ .node .label text,#chatgpt-mermaid-_r_267_ .image-shape .label,#chatgpt-mermaid-_r_267_ .icon-shape .label{text-anchor:middle;}#chatgpt-mermaid-_r_267_ .node .katex path{fill:#000;stroke:#000;stroke-width:1px;}#chatgpt-mermaid-_r_267_ .rough-node .label,#chatgpt-mermaid-_r_267_ .node .label,#chatgpt-mermaid-_r_267_ .image-shape .label,#chatgpt-mermaid-_r_267_ .icon-shape .label{text-align:center;}#chatgpt-mermaid-_r_267_ .node.clickable{cursor:pointer;}#chatgpt-mermaid-_r_267_ .root .anchor path{fill:rgb(175, 175, 175)!important;stroke-width:0;stroke:rgb(175, 175, 175);}#chatgpt-mermaid-_r_267_ .arrowheadPath{fill:rgb(175, 175, 175);}#chatgpt-mermaid-_r_267_ .edgePath .path{stroke:rgb(175, 175, 175);stroke-width:1px;}#chatgpt-mermaid-_r_267_ .flowchart-link{stroke:rgb(175, 175, 175);fill:none;}#chatgpt-mermaid-_r_267_ .edgeLabel{background-color:rgb(0, 0, 0);text-align:center;}#chatgpt-mermaid-_r_267_ .edgeLabel p{background-color:rgb(0, 0, 0);}#chatgpt-mermaid-_r_267_ .edgeLabel rect{opacity:0.5;background-color:rgb(0, 0, 0);fill:rgb(0, 0, 0);}#chatgpt-mermaid-_r_267_ .labelBkg{background-color:rgba(0, 0, 0, 0.5);}#chatgpt-mermaid-_r_267_ .cluster rect{fill:rgb(48, 48, 48);stroke:rgba(255, 255, 255, 0.15);stroke-width:1px;}#chatgpt-mermaid-_r_267_ .cluster text{fill:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ .cluster span{color:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ div.mermaidTooltip{position:absolute;text-align:center;max-width:200px;padding:2px;font-family:-apple-system-body,ui-sans-serif,-apple-system,system-ui,"Segoe UI",Helvetica,"Apple Color Emoji",Arial,sans-serif,"Segoe UI Emoji","Segoe UI Symbol";font-size:12px;background:rgb(48, 48, 48);border:1px solid rgba(255, 255, 255, 0.15);border-radius:2px;pointer-events:none;z-index:100;}#chatgpt-mermaid-_r_267_ .flowchartTitleText{text-anchor:middle;font-size:18px;fill:rgb(237, 237, 237);}#chatgpt-mermaid-_r_267_ rect.text{fill:none;stroke-width:0;}#chatgpt-mermaid-_r_267_ .icon-shape,#chatgpt-mermaid-_r_267_ .image-shape{background-color:rgb(0, 0, 0);text-align:center;}#chatgpt-mermaid-_r_267_ .icon-shape p,#chatgpt-mermaid-_r_267_ .image-shape p{background-color:rgb(0, 0, 0);padding:2px;}#chatgpt-mermaid-_r_267_ .icon-shape .label rect,#chatgpt-mermaid-_r_267_ .image-shape .label rect{opacity:0.5;background-color:rgb(0, 0, 0);fill:rgb(0, 0, 0);}#chatgpt-mermaid-_r_267_ .label-icon{display:inline-block;height:1em;overflow:visible;vertical-align:-0.125em;}#chatgpt-mermaid-_r_267_ .node .label-icon path{fill:currentColor;stroke:revert;stroke-width:revert;}#chatgpt-mermaid-_r_267_ .node .neo-node{stroke:rgb(31, 78, 148);}#chatgpt-mermaid-_r_267_ [data-look="neo"].node rect,#chatgpt-mermaid-_r_267_ [data-look="neo"].cluster rect,#chatgpt-mermaid-_r_267_ [data-look="neo"].node polygon{stroke:url(#chatgpt-mermaid-_r_267_-gradient);filter:drop-shadow( 1px 2px 2px rgba(185,185,185,1));}#chatgpt-mermaid-_r_267_ [data-look="neo"].swimlane.cluster rect{filter:none;}#chatgpt-mermaid-_r_267_ [data-look="neo"].node path{stroke:url(#chatgpt-mermaid-_r_267_-gradient);stroke-width:1px;}#chatgpt-mermaid-_r_267_ [data-look="neo"].node .outer-path{filter:drop-shadow( 1px 2px 2px rgba(185,185,185,1));}#chatgpt-mermaid-_r_267_ [data-look="neo"].node .neo-line path{stroke:rgb(31, 78, 148);filter:none;}#chatgpt-mermaid-_r_267_ [data-look="neo"].node circle{stroke:url(#chatgpt-mermaid-_r_267_-gradient);filter:drop-shadow( 1px 2px 2px rgba(185,185,185,1));}#chatgpt-mermaid-_r_267_ [data-look="neo"].node circle .state-start{fill:#000000;}#chatgpt-mermaid-_r_267_ [data-look="neo"].icon-shape .icon{fill:url(#chatgpt-mermaid-_r_267_-gradient);filter:drop-shadow( 1px 2px 2px rgba(185,185,185,1));}#chatgpt-mermaid-_r_267_ [data-look="neo"].icon-shape .icon-neo path{stroke:url(#chatgpt-mermaid-_r_267_-gradient);filter:drop-shadow( 1px 2px 2px rgba(185,185,185,1));}#chatgpt-mermaid-_r_267_ .node text{font-size:14px;font-weight:600;letter-spacing:normal;fill:rgb(153, 206, 255);}#chatgpt-mermaid-_r_267_ .edgeLabels text{font-size:13px;font-weight:600;letter-spacing:-0.08px;fill:rgb(153, 206, 255);}#chatgpt-mermaid-_r_267_ .node tspan[font-weight="normal"],#chatgpt-mermaid-_r_267_ .edgeLabels tspan[font-weight="normal"]{font-weight:600;}#chatgpt-mermaid-_r_267_ .edgeLabel .label rect{opacity:1;rx:13px;ry:13px;fill:rgb(0, 14, 26);stroke:rgb(26, 62, 95);stroke-width:1px;}#chatgpt-mermaid-_r_267_ .node rect,#chatgpt-mermaid-_r_267_ .node circle,#chatgpt-mermaid-_r_267_ .node ellipse,#chatgpt-mermaid-_r_267_ .node polygon,#chatgpt-mermaid-_r_267_ .node path{fill:rgb(0, 40, 77);stroke:rgba(255, 255, 255, 0.1);stroke-width:1px;}#chatgpt-mermaid-_r_267_ .node rect{rx:16px;ry:16px;}#chatgpt-mermaid-_r_267_ .mermaid-decision .label-container{fill:rgb(0, 14, 26);stroke:rgb(26, 62, 95);stroke-dasharray:2,2;}#chatgpt-mermaid-_r_267_ .edgePaths .flowchart-link{stroke:rgb(175, 175, 175);stroke-width:1px;stroke-linecap:round;stroke-linejoin:round;}#chatgpt-mermaid-_r_267_ .marker{fill:rgb(175, 175, 175);stroke:rgb(175, 175, 175);}#chatgpt-mermaid-_r_267_ :root{--mermaid-font-family:-apple-system-body,ui-sans-serif,-apple-system,system-ui,"Segoe UI",Helvetica,"Apple Color Emoji",Arial,sans-serif,"Segoe UI Emoji","Segoe UI Symbol";}Solicitud del usuarioIdentificar usuario, roles ypermisos¿Tiene permiso para laoperación?DenegarCargar recurso y relaciones¿Cumple alcance institucional,sede y relaciones?Validar estado y reglas delproceso¿Operación válida?Ejecutar operación autorizadaRegistrar auditoría cuandocorrespondaNoSíNoSíNoSí





La comprobación de alcance debe realizarse en el servidor antes de consultar o modificar datos. La interfaz puede ocultar acciones no permitidas, pero eso no sustituye la autorización.
6. Conflicto que debe resolverse antes de implementar
Hay una incompatibilidad entre dos decisiones aprobadas anteriormente:
- La regla inicial reservaba la eliminación a AI, REC y VREC, cuando no hubiera registros dependientes.
- La decisión posterior establece que AI y VREC solo pueden consultar la información de la institución y sus sedes, además de la aprobación de los procesos que les correspondan.
La segunda regla parece excluir la eliminación general de los seis módulos para AI y VREC, pero no se explicitó si reemplaza la regla anterior de eliminación.

Mi recomendación es aplicar la decisión más reciente: solo REC tendría permiso general de eliminación en estos seis módulos, condicionado a que no existan dependencias. AI y VREC conservarían sus facultades de consulta institucional y las aprobaciones de procesos expresamente asignados.

¿Confirmas que esta interpretación sustituye la regla anterior de eliminación para AI y VREC? Con esa confirmación podremos cerrar la especificación técnica sin contradicciones. No se ha modificado código.
