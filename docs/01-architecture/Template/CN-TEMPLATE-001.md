📖 CN-TEMPLATE-001
Cubierta 1 — Estructura Física del Template Oficial

Propongo la siguiente organización dentro del proyecto.

stubs/
│
└── module/
    │
    ├── app/
    │   ├── Core/
    │   │   ├── Actions/
    │   │   ├── Contracts/
    │   │   ├── Repositories/
    │   │   └── Services/
    │   │
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   └── Requests/
    │   │
    │   └── Models/
    │
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    │
    ├── resources/
    │   └── views/
    │       └── module/
    │           ├── index.blade.php
    │           ├── create.blade.php
    │           ├── edit.blade.php
    │           ├── show.blade.php
    │           └── _form.blade.php
    │
    ├── routes/
    │
    └── docs/
