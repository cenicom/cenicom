CN UI Framework v1.0
══════════════════════════════════════

01. Layout
──────────────────────
✅ page
✅ page-header
✅ card

02. Actions
──────────────────────
✅ button

03. Display
──────────────────────
✅ badge
✅ table
🟡 empty-state

04. Forms
──────────────────────
⏳ input
⏳ select
⏳ textarea
⏳ checkbox
⏳ radio
⏳ switch

05. Feedback
──────────────────────
⏳ alert
⏳ toast
⏳ modal

06. Navigation
──────────────────────
⏳ pagination
⏳ breadcrumb
⏳ tabs

CN Forms

x-cn.input

x-cn.select

x-cn.textarea

x-cn.checkbox

x-cn.radio

x-cn.switch

x-cn.field

CN-FORMS-001
x-cn.field
        ↓
CN-FORMS-002
x-cn.input
        ↓
CN-FORMS-003
x-cn.select
        ↓
CN-FORMS-004
x-cn.textarea
        ↓
Migración completa de create/edit de Currency
        ↓
Patrón certificado

Separar "Framework" de "Widgets"

No todo debe ser un componente base. Propongo distinguir entre:

CN UI Framework
│
├── Componentes base
│   ├── field
│   ├── input
│   ├── button
│   ├── table
│   └── badge
│
└── Widgets
    ├── currency-selector
    ├── country-selector
    ├── city-selector
    ├── datatable
    └── file-upload


    📦 Orden de construcción
CN-FORMS-001
x-cn.field

Responsable de:

estructura;
label;
obligatorio;
ayuda;
error;
espaciado.
CN-FORMS-002
x-cn.input

Responsable únicamente del <input>.

No conocerá:

labels;
errores;
ayuda.
CN-FORMS-003
x-cn.select

Misma filosofía.

CN-FORMS-004
x-cn.textarea

Misma filosofía.
