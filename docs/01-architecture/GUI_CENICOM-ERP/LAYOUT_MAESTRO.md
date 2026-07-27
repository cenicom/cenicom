🏛️ Propuesta del Layout Maestro

La distribución sería la siguiente:

┌──────────────────────────────────────────────────────────────────────────────┐
│ TOPBAR                                                                       │
│ Logo │ Buscador │ Accesos rápidos │ Notificaciones │ Usuario │ Configuración │
├───────────────┬──────────────────────────────────────────────────────────────┤
│               │ Breadcrumb                                                   │
│               ├──────────────────────────────────────────────────────────────┤
│               │ Header del módulo                                            │
│   SIDEBAR     │ Título                                                       │
│               │ Descripción                                                  │
│               ├──────────────────────────────────────────────────────────────┤
│               │ Toolbar                                                      │
│               │ Nuevo │ Exportar │ Importar │ Acciones                       │
│               ├──────────────────────────────────────────────────────────────┤
│               │                                                              │
│               │              Área principal de trabajo                       │
│               │                                                              │
│               │                                                              │
│               │                                                              │
│               │                                                              │
│               │                                                              │
├───────────────┴──────────────────────────────────────────────────────────────┤
│ Footer                                                                       │
└──────────────────────────────────────────────────────────────────────────────┘
📦 Zonas funcionales

Cada zona tendrá una responsabilidad clara.

Zona	Responsabilidad
Topbar	Navegación global y acciones del usuario
Sidebar	Navegación principal por módulos
Breadcrumb	Ubicación dentro del sistema
Header	Contexto de la pantalla actual
Toolbar	Acciones principales del módulo
Workspace	Contenido principal
Footer	Información institucional y versión

Esta separación facilitará la reutilización y el mantenimiento.

📏 Principios de proporción

Como punto de partida, propondría:

Topbar: altura fija y compacta.
Sidebar: ancho fijo en escritorio y colapsable.
Workspace: ancho fluido, ocupando el espacio disponible.
Footer: discreto, sin restar protagonismo al contenido.

No fijaremos píxeles todavía; primero acordaremos las relaciones entre las zonas. Los valores concretos podrán ajustarse durante la implementación.

🧩 Integración con el CN UI Framework

Cada zona del layout deberá corresponder a un componente reutilizable.

Un posible árbol sería:

x-cn.layout
│
├── x-cn.topbar
├── x-cn.sidebar
├── x-cn.breadcrumb
├── x-cn.page-header
├── x-cn.toolbar
├── x-cn.workspace
└── x-cn.footer

De esta manera, cualquier cambio en un componente se reflejará automáticamente en todos los módulos.

🔄 Integración con el CN Generator

El generador no construirá la estructura del layout.

Simplemente insertará el contenido específico del módulo dentro del área de trabajo.

Conceptualmente, el resultado será:

<x-cn.layout>
    <x-slot:breadcrumb>
        ...
    </x-slot:breadcrumb>

    <x-slot:header>
        ...
    </x-slot:header>

    <x-slot:toolbar>
        ...
    </x-slot:toolbar>

    <x-slot:content>
        <!-- Contenido generado automáticamente -->
    </x-slot:content>
</x-cn.layout>

Esto mantiene una única estructura para todo el ERP y simplifica el mantenimiento.

📋 Criterios de aceptación de CN-UI-001.1.1

La maniobra se considerará completada cuando el Layout Maestro:

✅ Defina claramente las zonas funcionales.
✅ Sea reutilizable por cualquier módulo.
✅ Sea compatible con el CN Generator.
✅ Sea responsive por diseño.
✅ Favorezca una experiencia de usuario consistente.
✅ Sirva como base para el CN UI Framework.
📖 Bitácora de la misión

Misión: CN-UI-001 — Construcción del Prototipo Maestro de la Interfaz de CENICOM ERP.
Fase: CN-UI-001.1 — Arquitectura Visual.
Maniobra activa: CN-UI-001.1.1 — Diseño del Layout Maestro.

🚢 La primera piedra ha sido colocada. Con este Layout Maestro establecemos la estructura sobre la que descansarán todos los módulos presentes y futuros de CENICOM ERP. A partir de aquí, cada nueva decisión fortalecerá una arquitectura visual coherente, mantenible y preparada para crecer junto al CN Generator. Buen viento y buena mar, capitán.

Layout Maestro aprobado, recordar que ya se c
