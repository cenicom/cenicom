⚓ CN-SEC-PERSIST queda oficialmente como el siguiente frente de trabajo.

La base actual está bien preparada: ya tenemos la capa de dominio de seguridad, pero todavía no existe persistencia real de roles/permisos en la base de datos.

CN-SEC-PERSIST — Objetivo

Pasar de:

User
  │
  └── IdentityService
        │
        └── roles()/permissions()  ← todavía no persistidos

a:

┌──────────────┐
│    users     │
└──────┬───────┘
       │
       │ belongsToMany
       ▼
┌──────────────┐
│     roles    │
└──────┬───────┘
       │
       │ belongsToMany
       ▼
┌──────────────┐
│ permissions  │
└──────────────┘

y finalmente:

Auth
 │
 ▼
User
 │
 ├── roles
 │     └── permissions
 │
 └── direct permissions
        │
        ▼
IdentityService
        │
        ▼
IdentityInterface
        │
        ▼
Authorization
        │
        ▼
Navigation
1. Modelo de persistencia

Propongo este esquema mínimo:

roles
-----
id
name
label
timestamps

permissions
-----------
id
name
description
module
timestamps

role_user
---------
role_id
user_id

permission_role
---------------
permission_id
role_id

permission_user
---------------
permission_id
user_id

Esto mantiene una separación importante:

Role = identidad funcional.
Permission = capacidad.
Role ↔ Permission = permisos heredados.
User ↔ Role = pertenencia.
User ↔ Permission = permisos directos.

No vamos a eliminar los registros actuales RoleRegistry y PermissionRegistry. Son registros de definiciones del runtime; la base de datos será la fuente de asignaciones persistentes.

Secuencia de implementación

No conviene crear todo de una vez.

CN-SEC-PERSIST-001

Schema de seguridad

Crear:

create_roles_table
create_permissions_table
create_role_user_table
create_permission_role_table
create_permission_user_table
CN-SEC-PERSIST-002

Modelos Eloquent

Crear:

app/Models/Role.php
app/Models/Permission.php

y agregar relaciones a:

app/Models/User.php
CN-SEC-PERSIST-003

Relaciones y contratos

Validar:

$user->roles()
$user->permissions()

$role->users()
$role->permissions()

$permission->roles()
$permission->users()
CN-SEC-PERSIST-004

IdentityService real

Modificar la resolución actual:

private function resolveRoles(object $user): array

y:

private function resolvePermissions(object $user): array

para que funcionen con las relaciones persistentes.

Además hay una decisión importante aquí:

User permissions
       +
Role permissions
       ↓
Effective permissions
       ↓
Identity

Los permisos efectivos deben ser la unión de ambos conjuntos, eliminando duplicados.

CN-SEC-PERSIST-005

Pruebas de integración

Probar con base de datos:

guest
authenticated user
user with role
role with permissions
user with direct permission
user with role + direct permission
duplicate permission
multiple roles
CN-SEC-PERSIST-006

Integración final CN-NAV

La prueba definitiva será:

DB User
   ↓
Role
   ↓
Permission
   ↓
IdentityService
   ↓
IdentityInterface
   ↓
NavigationService
   ↓
Navigation permission
   ↓
Sidebar
Primer hito: CN-SEC-PERSIST-001

Yo empezaría únicamente por las migraciones.

No toquemos todavía User.php, IdentityService, ni los registries.

Primero dejamos una estructura de base de datos sólida y comprobable.

La primera entrega será:

database/migrations/
├── xxxx_create_roles_table.php
├── xxxx_create_permissions_table.php
├── xxxx_create_role_user_table.php
├── xxxx_create_permission_role_table.php
└── xxxx_create_permission_user_table.php

acompañada por pruebas de esquema.

Objetivo del primer hito: que la persistencia exista sin alterar todavía el comportamiento de los 626 tests verdes.

⚓ CN-SEC-PERSIST-001 — Schema de seguridad. Ese es el siguiente punto de implementación.
