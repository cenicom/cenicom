═══════════════════════════════════════════════
SPRINT III.1

CN-TEMPLATE-001

Plantilla Oficial de Módulos
ERP CENICOM
═══════════════════════════════════════════════

Objetivo General

Definir un estándar técnico que garantice que todos los módulos del ERP compartan:

La misma arquitectura.
El mismo flujo de trabajo.
La misma organización.
La misma calidad.
La misma experiencia para el desarrollador.

🧭 Plan de navegación

Propongo dividir el sprint en seis hitos principales.

Hito 1 — Arquitectura del módulo (📍Comenzamos aquí)

Aquí definiremos el "esqueleto" que todo módulo deberá seguir.

Estructura lógica
Módulo
│
├── Dominio
│   ├── Model
│   ├── Repository
│   ├── Service
│   ├── Actions
│   └── Contracts
│
├── Aplicación
│   ├── Requests
│   ├── Controller
│   └── Policies
│
├── Presentación
│   ├── Routes
│   ├── Views
│   └── CN UI
│
└── Persistencia
    ├── Migration
    ├── Factory
    └── Seeder

Este diagrama servirá como referencia para todos los desarrolladores.

📜 ADR-005

Título: Paralelismo estructural entre el Template y el proyecto.

Estado: Aceptada.

Decisión:

El Template Oficial replicará la estructura de carpetas del proyecto real. La única diferencia será el uso de marcadores ({{Module}}, {{module}}, etc.) en lugar de nombres concretos.

Beneficios:

Consistencia.
Facilidad de mantenimiento.
Automatización futura.
Curva de aprendizaje reducida.
⚓ Nueva Carta de Navegación

A partir de ahora, todas las maniobras seguirán este formato:

📍 Cubierta:
III

📍 Destino:
stubs/cn-module/app/Core/Contracts/

📍 Construcción:
{{Module}}RepositoryInterface.php

{{Module}}ServiceInterface.php

📍 Estado:
En construcción

No volveremos a dar una orden sin indicar el destino físico.
