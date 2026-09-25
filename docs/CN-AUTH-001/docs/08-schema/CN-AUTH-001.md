Con el alcance ya establecido: autenticación HTTP, sesión web, /login, /logout, credenciales email/password, regeneración/invalidez de sesión y separación estricta respecto de autorización y verificación.

2. Después: pruebas primero

Las pruebas deben demostrar el contrato, no una implementación concreta. La secuencia será:

ID	Evidencia
AUTH-001	GET /login responde 200
AUTH-002	formulario contiene email y contraseña
AUTH-003	credenciales inválidas no autentican
AUTH-004	credenciales válidas autentican
AUTH-005	login regenera la sesión
AUTH-006	usuario autenticado puede acceder a /countries
AUTH-007	usuario no autenticado es llevado a /login
AUTH-008	POST /logout termina la sesión
AUTH-009	después del logout /countries vuelve a requerir autenticación
AUTH-010	IdentityService reconoce al usuario autenticado
AUTH-011	login no modifica roles/permisos
AUTH-012	el flujo continúa siendo compatible con Security/Navigation

Importante: las pruebas AUTH-006/007 son especialmente valiosas porque convierten el error actual:

Route [login] not defined

en una evidencia contractual de la ausencia del runtime de autenticación, en lugar de tratarlo como un problema de Country.

3. Regla de cambio

Primero esperamos que las pruebas fallen de la forma esperada por ausencia del runtime.

Después:

evidencia → contrato → impacto → corrección

y únicamente entonces implementamos los artefactos HTTP necesarios.

Así mantenemos intacta la frontera:

CN-AUTH-001
    │
    ├── HTTP Login/Logout
    │
    ├── Laravel web guard
    │
    ├── User
    │
    └── IdentityService
             │
             ▼
       CN Security
       Authorization
       Navigation

Y dejamos email verification fuera de esta maniobra, reservado para CN-AUTH-002.

Siguiente paso concreto: crear CN-AUTH-001.md y verificarlo antes de escribir una sola línea del runtime.
/*/-*/*-/*-/*-/-*/*-/*-/*-/-*/*-/*-/-*/*-/*-/*-/*-/-*/*-/*-/*-/*-/-*/*-/*-/*-/*-/*-/-*/*-/*-/*-/*-/*-/*-/*-
cat > docs/08-schema/CN-AUTH-001.md <<'EOF'
# CN-AUTH-001 — Autenticación HTTP

## 1. Identificación

- Código: CN-AUTH-001
- Área: Core / Security / Authentication
- Estado: 🟢 Contrato definido
- Alcance: Autenticación HTTP y gestión de sesión
- Dependencias: Laravel Web Guard, `App\Models\User`, `IdentityService`
- Contrato relacionado: CN-AUTH-002 — Email Verification

---

## 2. Objetivo

Definir el contrato de autenticación HTTP de CENICOM ERP para permitir:

1. iniciar sesión mediante credenciales válidas;
2. mantener la sesión autenticada mediante el guard web existente;
3. cerrar la sesión de forma explícita;
4. proteger los recursos HTTP que requieren autenticación;
5. integrar la autenticación Laravel con la identidad CENICOM.

Este contrato no define autorización, roles, permisos ni verificación de correo electrónico.

---

## 3. Alcance

CN-AUTH-001 comprende exclusivamente:

- presentación del formulario de inicio de sesión;
- recepción de credenciales;
- validación de credenciales;
- establecimiento de sesión;
- regeneración de sesión después del login;
- cierre de sesión;
- invalidación de sesión;
- protección HTTP mediante middleware `auth`;
- resolución de la identidad autenticada por `IdentityService`.

Quedan fuera de este contrato:

- autorización;
- roles;
- permisos;
- navegación condicionada por permisos;
- recuperación de contraseña;
- cambio de contraseña;
- registro público de usuarios;
- verificación de correo electrónico;
- MFA/2FA;
- SSO;
- proveedores externos de identidad.

---

## 4. Actores y responsabilidades

### 4.1 Usuario

Proporciona sus credenciales:

- email;
- password.

### 4.2 HTTP Authentication

Responsable de:

- recibir las solicitudes `/login` y `/logout`;
- validar las credenciales;
- iniciar la sesión;
- cerrar la sesión;
- respetar el guard web existente.

### 4.3 Laravel Authentication

Responsable de:

- resolver el usuario mediante el guard `web`;
- verificar las credenciales;
- mantener el estado autenticado en la sesión.

### 4.4 `App\Models\User`

Representa al usuario autenticable persistido en la aplicación.

CN-AUTH-001 no modifica el modelo `User`.

### 4.5 `IdentityService`

Responsable de traducir el usuario autenticado de Laravel a la identidad utilizada por CENICOM.

El servicio de identidad no autentica directamente las credenciales.

### 4.6 Authorization / Security

La autorización permanece separada de la autenticación.

Roles y permisos no forman parte del proceso de login.

---

## 5. Guard de autenticación

El contrato utiliza el guard Laravel existente:

```text
web
