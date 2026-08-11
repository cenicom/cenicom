⚓ Próxima maniobra: CN-SEC-PERSIST-002

Ahora toca persistir y recuperar roles/permisos reales.

El orden que recomiendo es:

Auditar los seis esquemas migrados
claves primarias
índices
foreignId
cascadeOnDelete
restricciones únicas
Completar relaciones de User
roles()
permissions()
Crear modelos Role y Permission
sin meter lógica de autorización en los modelos.
Adaptar IdentityService
para que:
$user
    ->roles()
    ->pluck('name')
    ->toArray();

y:

$user
    ->permissions()
    ->pluck('name')
    ->toArray();

sean datos provenientes realmente de la BD.

Crear pruebas de integración:
SecurityIdentityPersistenceTest

con escenarios:

usuario autenticado
        │
        ├── rol admin
        │
        ├── permiso users.view vía rol
        │
        └── permiso users.delete directo
Finalmente comprobar el flujo completo:
HTTP Request
   ↓
Laravel Auth
   ↓
User
   ↓
IdentityService
   ↓
IdentityInterface
   ↓
NavigationViewComposer
   ↓
NavigationService
   ↓
PermissionResolver
   ↓
Navigation filtrada

No tocaría todavía PermissionRegistry ni RoleRegistry. Esos registros tienen otra responsabilidad: definiciones/runtime. La persistencia debe alimentar la identidad sin contaminar ese mecanismo.

Estado actual:

🟢 CN-SEC-PERSIST-001 — Schema de seguridad: COMPLETADO
🟢 Migración desde cero: OK
🟢 626 tests / 1377 assertions: OK
🟡 CN-SEC-PERSIST-002 — Modelos y relaciones Eloquent: SIGUIENTE
