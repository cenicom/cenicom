1. Filosofía de Diseño

La identidad visual de CENICOM estará basada en cinco principios:

Claridad.
Consistencia.
Accesibilidad.
Productividad.
Elegancia sin excesos.

Queremos que el usuario se concentre en su trabajo, no en descifrar la interfaz.

2. Paleta Institucional

Propongo una paleta moderna, sobria y atemporal.

Uso	Color	Hex
Primario	Azul Institucional	#1E3A8A
Secundario	Azul Claro	#2563EB
Éxito	Verde	#16A34A
Advertencia	Ámbar	#F59E0B
Error	Rojo	#DC2626
Información	Celeste	#0EA5E9
Administración	Morado	#7C3AED
Fondo	Gris muy claro	#F8FAFC
Tarjetas	Blanco	#FFFFFF
Texto principal	Gris oscuro	#1F2937
Texto secundario	Gris medio	#6B7280
Bordes	Gris claro	#E5E7EB
3. Significado de los colores

Esta regla será obligatoria en todo el ERP.

Acción	Color
Crear	🟢 Verde
Guardar	🟢 Verde
Consultar	🔵 Azul
Editar	🟡 Ámbar
Eliminar	🔴 Rojo
Configuración	🟣 Morado
Exportar	🔵 Azul
Imprimir	⚪ Gris
Cancelar	⚪ Gris
Ayuda	🔵 Celeste

Así, el usuario reconocerá la acción incluso antes de leer el texto.

4. Tipografía

Propongo utilizar:

Inter

¿Por qué?

Excelente legibilidad.
Muy utilizada en aplicaciones empresariales.
Optimizada para pantallas.
Soporta perfectamente caracteres en español.

Si en algún momento no está disponible, la pila será:

Inter,
Segoe UI,
Roboto,
Helvetica,
Arial,
sans-serif
5. Iconografía

Toda la aplicación utilizará una sola biblioteca.

Continuaremos con:

Font Awesome 7

Ya la conoces y cubre prácticamente todas las necesidades del ERP.

6. Espaciado

Utilizaremos una escala fija.

Tamaño	Valor
XS	4px
SM	8px
MD	16px
LG	24px
XL	32px
XXL	48px

Nunca utilizaremos valores arbitrarios.

7. Bordes

Todos los componentes compartirán el mismo radio.

8px

Esto aporta uniformidad visual.

8. Sombras

Muy discretas.

No queremos una interfaz recargada.

Tres niveles serán suficientes:

Baja
Media
Alta
9. Botones

Todos tendrán la misma altura.

40px

Con variantes:

Primary
Success
Warning
Danger
Secondary
Outline
10. Tablas

Las tablas serán una de las piezas más importantes del ERP.

Deberán incluir de forma estándar:

Encabezado fijo (cuando sea útil).
Ordenamiento.
Búsqueda.
Paginación.
Exportación (Excel, PDF, CSV e impresión).
Estado vacío con mensaje claro.
Indicador de carga.
11. Formularios

Todos compartirán las mismas reglas:

Etiqueta superior.
Campo.
Texto de ayuda opcional.
Mensaje de validación.
Indicador de obligatorio.
Consistencia en márgenes y alineación.
12. Modo oscuro

Quedará soportado desde el inicio mediante variables CSS.

No será necesario rehacer componentes en el futuro.

13. Identidad Visual

Quiero que CENICOM sea reconocible por:

Diseño limpio.
Colores institucionales consistentes.
Iconografía uniforme.
Mucho espacio en blanco.
Formularios claros.
Tablas fáciles de leer.
Navegación intuitiva.

No competiremos por tener la interfaz "más llamativa", sino por ofrecer la experiencia más eficiente para el trabajo diario.

CN Foundation

Sprint 5.5
⏳ CN Response

Sprint 5.6
⏳ BaseController

Sprint 5.7
⏳ BaseCrudController

Sprint 5.8
⏳ CurrencyController

Sprint 5.9
⏳ Currency Views

Sprint 6.0
⏳ Country

Nuestra próxima misión

Mi recomendación es que la siguiente sesión la dediquemos exclusivamente a un objetivo:

Completar al 100 % el módulo Currency y declararlo como el módulo patrón oficial de CENICOM.

CENICOM

CN Design System

01. Introducción

02. Filosofía

03. Colores

04. Tipografía

05. Iconografía

06. Espaciado

07. Grid

08. Botones

09. Formularios

10. Tablas

11. Tarjetas

12. Alertas

13. Modales

14. Navegación

15. Carga de archivos

16. Dashboard

17. Reportes

18. Accesibilidad

19. Animaciones

20. Buenas prácticas

Sprint 1
✅ CN Button
✅ CN Input
✅ CN Select (Select2)
✅ CN Textarea

Sprint 2
✅ CN File
✅ CN Image
✅ CN Avatar
✅ CN Password

Sprint 3
✅ CN Table
✅ CN Table Actions
✅ CN Pagination

Sprint 4
✅ CN Alerts
✅ CN Confirm
✅ CN Toast
✅ CN Modal

Sprint 5
✅ CN Design System


resources/css/cn/

base/
    variables.css
    typography.css
    spacing.css

layout/
    page.css
    sidebar.css
    topbar.css
    footer.css

components/
    button.css
    badge.css
    card.css
    alert.css
    modal.css

forms/
    input.css
    select.css
    textarea.css
    checkbox.css

tables/
    table.css
    datatable.css
    pagination.css

utilities/
    helpers.css


resources/js/cn/

core/
    cn.js

components/
    modal.js
    dropdown.js

tables/
    datatable.js
    pagination.js

forms/
    select.js
    validation.js

alerts/
    sweetalert.js

helpers/
    ajax.js
    clipboard.js

    CN UI Framework v1.0

FASE 1
├── page
├── page-header
├── page-title
└── page-actions

FASE 2
├── card
├── card-header
├── card-body
└── card-footer

FASE 3
├── button
├── badge
├── icon
└── tooltip

FASE 4
├── table
├── datatable
├── table-actions
├── pagination
└── empty-state

FASE 5
├── input
├── select
├── textarea
├── checkbox
├── radio
├── switch
├── date
└── password

FASE 6
├── modal
├── alert
├── toast
└── confirm

FASE 7
├── dashboard widgets
├── statistics
├── charts
└── timeline

CN-0003  x-cn.button        🔨
CN-0004  x-cn.badge         ⏳
CN-0005  x-cn.icon          ⏳
CN-0006  x-cn.table         ⏳
CN-0007  x-cn.table-actions ⏳
