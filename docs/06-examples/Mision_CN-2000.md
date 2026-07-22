Misión CN-2000
Etapa II — Validación y Calidad

La dividiría en pequeñas entregas, como hemos venido trabajando.

Entrega 1 — ManifestValidator

Objetivo

Antes de generar un módulo, validar completamente el manifiesto.

php artisan cn:make-module Currency

El flujo sería:

ManifestLoader
        │
        ▼
ManifestValidator
        │
        ├── OK
        ▼
ModuleDataFactory
        │
        ▼
ModuleGenerator
Responsabilidades

Validar:

identity
generation
database
fields
relations
permissions
navigation
metadata

y emitir errores claros.

Ejemplo:

Field "status"

Unknown type:
status_enum
Arquitectura
Support/

    ManifestLoader.php

Validation/

    ManifestValidator.php

Exceptions/

    InvalidManifestException.php
Entrega 2 — Schema Validator

Una vez exista el manifiesto…

cada campo debe validarse.

Ejemplo

{
    "name":"email",
    "type":"string",
    "length":150,
    "nullable":false
}

Debe comprobar:

name obligatorio
type válido
length compatible
default compatible
unique compatible
foreignKey compatible
Entrega 3 — Generator Doctor

Comando

php artisan cn:doctor

Salida

CN Generator

✔ Stub Folder

✔ Manifest Folder

✔ Views

✔ Contracts

✔ Generator

✔ Presentation

✔ Laravel Version

✔ PHP Version
Entrega 4 — Module Audit
php artisan cn:audit Currency

Debe revisar

✔ Model

✔ Migration

✔ Controller

✔ Repository

✔ Service

✔ Requests

✔ Views

✔ Routes
Entrega 5 — Self Test
php artisan cn:self-test

Proceso

crear módulo temporal

↓

generar

↓

artisan test

↓

artisan pint

↓

eliminar módulo temporal
¿Y la mejora de las rutas?

No la descartaría.

La convertiría en la primera misión de la Etapa III (Mejoras Funcionales).

Porque esa mejora no corrige la estabilidad del framework; amplía sus capacidades.

Mi propuesta de hoja de ruta
ETAPA II

██████████

1 ManifestValidator
2 SchemaValidator
3 CN Doctor
4 ModuleAudit
5 Self Test

↓

ETAPA III

██████████

✓ Route Middleware

✓ Route Names

✓ Route Groups

✓ Policies

✓ API Generator

✓ Events

✓ Observers

✓ Factories

✓ Seeders

✓ Notifications

✓ Queues

✓ Broadcast
