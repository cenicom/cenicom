CN Foundation

Catálogos Base

Seguridad

Instituciones

Académico

Tesorería

Inventario

Reportes


Bloque A — Núcleo del Framework

Componentes:

ModuleGenerator
GeneratorRegistry
BaseGenerator
GeneratorInterface
GeneratorResult

App\Core\Generator\Support\
│
├── StubManager.php
├── FileWriter.php
├── PathResolver.php
├── NamespaceResolver.php
├── ManifestLoader.php
App\Core\Generator\Processors\
│
├── FormFieldProcessor.php
├── MigrationFieldProcessor.php
App\Core\Generator\Builders\
│
└── ColumnBuilder.php
App\Core\Generator\DTO\
│
├── ColumnDefinition.php
├── ModuleData.php   (Revisión final de integración)

Orden de navegación

Propongo mantener el mismo método de trabajo que tan buenos resultados dio en CNG-011:

Fase I: Auditoría completa de InputType.
Fase II: Refactorización completa de InputType.
Fase III: Auditoría de FieldType.
Fase IV: Evolución de FieldType hasta la versión 2.0.
Fase V: Ajuste de ColumnDefinition para aprovechar la nueva API.
Fase VI: Simplificación de los Processors usando las nuevas capacidades.

***********************************************************************************
La siguiente misión oficial será:

CNG-013 — Evolución de FieldType a la versión 2.0.

Una vez completada, el CN Generator tendrá los cuatro pilares fundamentales de su arquitectura:

✅ ModuleData
✅ ColumnDefinition
✅ InputType
🔜 FieldType

Con esos cuatro pilares consolidados, el resto del CN Generator podrá construirse con mucha mayor rapidez, manteniendo una arquitectura limpia, desacoplada y preparada para crecer durante muchos años.**************
