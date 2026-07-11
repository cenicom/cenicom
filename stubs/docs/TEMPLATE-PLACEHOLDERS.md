Ese documento respondería preguntas como:

¿Qué significa {{Module}}?
¿Cuándo usar {{module}}?
¿Cuál es la diferencia entre {{model}} y {{collection}}?
¿Qué marcadores son obligatorios?
¿Qué marcadores son opcionales?

Esto evitará inconsistencias y facilitará la automatización futura.

📋 Propuesta de estructura
docs/
└── TEMPLATE-PLACEHOLDERS.md

1. Introducción

2. Convenciones

3. Placeholders obligatorios

4. Placeholders opcionales

5. Ejemplos de sustitución

6. Reglas de nomenclatura

7. Ejemplo completo de un módulo generado

📜 ADR-008

Título: Catálogo oficial de Placeholders del CN Template.

Estado: Propuesta aprobada.

Decisión:

Todos los archivos del Template utilizarán exclusivamente los placeholders definidos en docs/TEMPLATE-PLACEHOLDERS.md. No se introducirán nuevos marcadores sin actualizar previamente este documento.

Beneficios:

Consistencia en todo el Template.
Base sólida para automatización futura.
Curva de aprendizaje reducida.
Mantenimiento simplificado.

⚓ Observación del Almirante

Durante esta implementación ha surgido una mejora para el Template: el placeholder {{columns}}. Me parece una incorporación coherente porque elimina una suposición que el Template no puede hacer por sí mismo. Antes de usarlo de forma definitiva, recomiendo añadirlo al documento TEMPLATE-PLACEHOLDERS.md para mantener la sincronización entre la especificación y el código.

| Placeholder      | Estado |
| ---------------- | ------ |
| `{{Module}}`     | ✅      |
| `{{module}}`     | ✅      |
| `{{model}}`      | ✅      |
| `{{collection}}` | ✅      |
| `{{title}}`      | ✅      |
| `{{plural}}`     | ✅      |
| `{{singular}}`   | ✅      |
| `{{icon}}`       | ✅      |

