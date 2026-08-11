🚩 Siguiente maniobra

CN-SEC-PERSIST-003 — Persistencia del Authorization / Permission Engine

La cadena que debemos demostrar ahora es:

┌─────────────┐
│    User     │
└──────┬──────┘
       │
       ├── roles ──────────────┐
       │                       ▼
       │                  ┌──────────┐
       │                  │   Role   │
       │                  └────┬─────┘
       │                       │
       │                       ▼
       │                  Permission
       │
       └── direct permissions
                │
                ▼
        ┌────────────────┐
        │ IdentityService│
        └───────┬────────┘
                │
                ▼
         IdentityInterface
                │
                ▼
       PermissionResolver
                │
                ▼
       AuthorizationService
                │
                ▼
          can(permission)

El objetivo será probar contra la base de datos real que:

un permiso directo concede acceso;
un permiso heredado por rol concede acceso;
un permiso inexistente lo deniega;
un permiso asignado a otro usuario no concede acceso;
permisos directos + heredados funcionan conjuntamente;
la navegación puede consumir esta autorización real.

No tocaría todavía NavigationService ni NavigationViewComposer. Primero dejamos blindado CN-SEC-PERSIST-003; después hacemos la integración con CN-NAV.

⚓ Estado actual:

CN-SEC-PERSIST
│
├── 001 Schema de seguridad ............. ✅
├── 002 Modelos y relaciones Eloquent ... ✅
└── 003 Authorization persistente ....... ▶ SIGUIENTE

Estamos en buen punto para entrar al siguiente tramo.
