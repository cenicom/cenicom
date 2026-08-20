| Elemento                             | Estado                            |
| ------------------------------------ | --------------------------------- |
| `ViewBuilder` / `ViewGenerator`      | ✅ Existe, pero pertenece a CN-GEN |
| `ViewDefinitionInterface`            | ❌ No existe                       |
| `ViewRegistryInterface`              | ❌ No existe                       |
| `ViewRegistrarInterface`             | ❌ No existe                       |
| `ViewDefinitionRegistry`             | ❌ No existe                       |
| `ViewDefinitionLoader`               | ❌ No existe                       |
| `ViewService`                        | ❌ No existe                       |
| `ModuleDefinition::$viewDefinitions` | ❌ No existe                       |
| vistas reales de `Institution`       | ❌ No existen                      |
| integración módulo → views           | ❌ No existe                       |

La secuencia correcta queda:

10.9.4.1 — Contrato

Crear, como mínimo:

app/Core/View/
├── Contracts/
│   ├── ViewDefinitionInterface.php
│   ├── ViewRegistrarInterface.php
│   ├── ViewRegistryInterface.php
│   └── ViewServiceInterface.php

Pero antes de crear código, debemos fijar qué representa una ViewDefinition.

No conviene copiar mecánicamente CRUD.

10.9.4.2 — Registro

Después:

ViewRegistrar
ViewRegistry
ViewService
10.9.4.3 — Loader modular

Algo equivalente a:

ViewDefinitionLoader

que recorra:

$module->viewDefinitions

igual que:

$module->crudDefinitions
10.9.4.4 — ModuleDefinition

Agregar únicamente la nueva colección:

viewDefinitions: []

manteniendo la compatibilidad con los módulos existentes.

10.9.4.5 — RED

Primero tests que demuestren que actualmente no existe la infraestructura esperada.

Por ejemplo, el equivalente conceptual a CRUD:

ViewContainerBindingsTest
ModuleViewIntegrationTest
10.9.4.6 — Implementación mínima

Solo lo necesario para satisfacer esos tests.

10.9.4.7 — Integración

Entonces conectar:

ModuleDefinition
       ↓
ViewDefinitionLoader
       ↓
ViewDefinitionRegistry
       ↓
ViewService / ViewRegistrar
       ↓
Laravel View

sin mezclar esto con:

CN-GUI
Blade components
ViewBuilder
ViewGenerator
generación de archivos
*********************************************************************
Por lo tanto, la siguiente misión concreta debería ser:

10.9.4.1 — ViewDefinitionRegistry

Crear/auditar:

app/Core/View/Registry/ViewDefinitionRegistry.php
tests/Unit/Core/View/Registry/ViewDefinitionRegistryTest.php

Con el mismo contrato mínimo que ya demostró funcionar en Navigation:

add(string $definition): void;


definitions(): array;


clear(): void;

Después:

10.9.4.2 — ViewDefinitionLoader

para consumir:

$module->viewDefinitions

y respetar:

if (! $module->enabled) {
    continue;
}

Después:

10.9.4.3 — ViewBootstrapper

para ejecutar:

$definition = app($definitionClass);


if (! $definition instanceof ViewDefinitionInterface) {
    continue;
}


$definition->register($registrar);

Y solo después de esos tres límites, integrar el ciclo en ViewServiceProvider/bindings, evitando tocar innecesariamente ModuleBootstrap, ModuleRegistry o CNFrameworkServiceProvider.

La siguiente operación inmediata es entonces auditar/crear ViewDefinitionRegistry, no modificar nuevamente ModuleDefinitionFactory.
