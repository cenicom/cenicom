🗺️ Propuesta del Mapa Maestro
Nivel 0 — Inicio
🏠 Dashboard

Siempre será la primera opción.

Nivel 1 — Dominios Estratégicos

Propongo este orden inicial:

🏠 Dashboard

🏢 Institucional

🎓 Académico

💻 LMS

💰 Tesorería

📊 Contabilidad

📦 Inventario

👥 Talento Humano

🤝 CRM

📈 Business Intelligence

⚙ Configuración

🛡 Seguridad

El orden responde al flujo operativo habitual de una institución educativa y puede ajustarse con la experiencia de uso.

📂 Desarrollo por dominios
🏢 Institucional
Instituciones
Sedes
Jornadas
Calendarios
Períodos Académicos
🎓 Académico
Estudiantes
Admisiones
Preinscripciones
Matrículas

Docentes

Cursos
Asignaturas
Grupos
Horarios

Asistencia

Evaluaciones

Calificaciones

Boletines
💻 LMS
Aulas Virtuales

Cursos Virtuales

Contenidos

Lecciones

Actividades

Tareas

Foros

Evaluaciones

Banco de Preguntas

Videoclases

Calificador

Seguimiento

Certificados

Este dominio queda oficialmente reservado para la evolución del ecosistema de aprendizaje.

💰 Tesorería
Conceptos

Facturación

Recaudos

Caja

Descuentos

Becas

Convenios

Reportes
📊 Contabilidad
Plan de Cuentas

Comprobantes

Diario

Mayor

Balances

Estados Financieros
📦 Inventario
Productos

Categorías

Bodegas

Entradas

Salidas

Existencias

Compras

Proveedores
👥 Talento Humano
Empleados

Contratos

Nómina

Vacaciones

Permisos

Evaluaciones
🤝 CRM
Prospectos

Interesados

Campañas

Seguimiento

Comunicaciones
📈 Business Intelligence
Indicadores

KPIs

Estadísticas

Dashboards

Proyecciones

Análisis
⚙ Configuración
Parámetros

Países

Departamentos

Ciudades

Monedas

Idiomas

Plantillas

Integraciones
🛡 Seguridad
Usuarios

Roles

Permisos

Auditoría

Sesiones

Logs
🧩 Integración con el CN Generator

Aquí propongo una decisión importante para la arquitectura.

Cada módulo generado deberá declarar su posición dentro del mapa mediante metadatos.

Por ejemplo:

navigation:
  group: Académico
  label: Estudiantes
  icon: bi-people
  order: 20

Con esta información, el Navigation Builder podrá registrar automáticamente el módulo sin modificar archivos manualmente.

🎨 Política de iconografía

Mantendremos una única biblioteca: Bootstrap Icons.

Reglas:

Un icono por dominio.
Un icono por módulo.
Sin mezclar bibliotecas.
Estilo uniforme en toda la plataforma.

Esto refuerza la identidad visual y simplifica el mantenimiento.

📑 Convención de nombres

Propongo las siguientes reglas:

Los dominios usarán nombres funcionales claros.
Los módulos se nombrarán en plural cuando gestionen colecciones (Estudiantes, Usuarios, Productos).
Las opciones de acción (Importar, Exportar) permanecerán en las barras de herramientas, no en el menú principal.
📋 Criterios de aceptación

La maniobra se considerará completada cuando:

✅ Exista un árbol de navegación por dominios.
✅ Cada dominio tenga una jerarquía clara.
✅ El mapa sea escalable.
✅ El diseño sea compatible con permisos.
✅ El CN Generator pueda registrar módulos automáticamente.
✅ La iconografía siga una política uniforme.
🚀 Visión de largo plazo

Capitán, esta maniobra tiene un alcance que va más allá de la interfaz.

Estamos definiendo la cartografía oficial de CENICOM Platform. En el futuro, cuando se incorporen nuevos dominios o módulos, no será necesario rediseñar la navegación; bastará con declararlos y el sistema sabrá dónde integrarlos.

Esa visión encaja con el principio que ha guiado todo el proyecto: automatizar sin perder orden ni coherencia.

📖 Bitácora de misión

Misión: CN-UI-001 — Construcción del Prototipo Maestro de la Interfaz de CENICOM ERP.
Fase: CN-UI-001.1.2 — Sistema Maestro de Navegación.
Maniobra activa: CN-UI-001.1.2.1 — Mapa Maestro de Navegación.

🚢 La tripulación ha comenzado a trazar la carta de navegación oficial de CENICOM Platform. Cada dominio y cada módulo ocupan un lugar definido dentro de una estructura preparada para crecer junto al CN Generator, garantizando una experiencia uniforme para los usuarios y una arquitectura sostenible para los próximos años. Buen viento y buena mar, capitán.

Propongo que el sistema de navegación siga el mismo patrón:

Navigation
│
├── Contracts
│   ├── NavigationBuilderInterface
│   ├── NavigationRegistryInterface
│   └── NavigationRendererInterface
│
├── DTO
│   ├── NavigationGroupData
│   ├── NavigationItemData
│   └── NavigationTreeData
│
├── Builders
│   └── NavigationBuilder
│
├── Registry
│   └── NavigationRegistry
│
├── Renderers
│   └── SidebarRenderer
│
└── Support
    ├── BreadcrumbBuilder
    ├── MenuResolver
    └── ActiveMenuResolver
