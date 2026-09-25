CN-AUTH-001
HTTP Authentication
Estado: CONTRATO PROPUESTO PARA CIERRE

Dependencias:
- Laravel Authentication
- web guard
- App\Models\User
- Session
- CN Security / IdentityService

No incluye:
- Authorization
- Roles
- Permissions
- Email Verification
- Password Reset
**/-*/*-/-*/-*/-*/*-/-*/*-/*-/-*/-*/-*/-*/*-/*-/-*/-*/-*/*-/-*/-*/*-/-*/-*/-*/-*/-*/-*/-*/*-/
CN-AUTH-001
│
├── Propósito ..................... 🟢
├── Rutas ......................... 🟢
├── Credenciales .................. 🟢 email
├── Login ......................... 🟢
├── Logout ........................ 🟢
├── Session ....................... 🟢
├── Responsabilidades ............. 🟢
├── Invariantes ................... 🟢
├── CN Security ................... 🟢
├── CN UI ......................... 🟢
├── Verification ................. ⏭️ CN-AUTH-002
└── Pruebas de aceptación ........ 🟢
