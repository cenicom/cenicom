Siguiente maniobra

CN-SEC-PERSIST-004 — Integración Security ↔ Navigation persistente.

Aquí debemos comprobar que la navegación ya no dependa solamente del registro en memoria, sino de los permisos persistidos en:

users
   │
   ├── role_user ──> roles
   │                  │
   │                  └── permission_role ──> permissions
   │
   └── permission_user ────────────────> permissions

Y validar el flujo completo:

Auth::login($user)
        ↓
IdentityService
        ↓
Identity
        ↓
PermissionResolver
        ↓
NavigationPermissionResolver
        ↓
NavigationBuilder
        ↓
árbol de navegación filtrado

Objetivo del siguiente hito: demostrar mediante Feature/Integration Tests que un usuario real de BD ve únicamente los nodos de navegación para los que tiene permiso, incluyendo permisos heredados por rol.

La persistencia de Identity + Roles + Permissions + Authorization ya está consolidada. Ahora toca llevar esa persistencia hasta Navigation.
