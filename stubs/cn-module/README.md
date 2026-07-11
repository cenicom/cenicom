📦 Orden de construcción

Propongo construir las clases en este orden:

Etapa 1 — Contracts
{{Module}}RepositoryInterface

{{Module}}ServiceInterface

¿Por qué primero?

Porque toda la arquitectura depende de los contratos.

Etapa 2 — Repository
{{Module}}Repository
Etapa 3 — Service
{{Module}}Service
Etapa 4 — Actions
Create{{Module}}Action

Update{{Module}}Action

Delete{{Module}}Action
Etapa 5 — Requests
Store{{Module}}Request

Update{{Module}}Request
Etapa 6 — Controller
{{Module}}Controller
Etapa 7 — Model
{{Module}}
🏛️ Una mejora al Template

Quiero proponer una mejora que creo será muy valiosa.

Hasta ahora usamos marcadores como:

{{Module}}

{{module}}

{{table}}

Sugiero oficializar un catálogo completo de marcadores para evitar ambigüedades:

Marcador	Ejemplo (Country)
{{Module}}	Country
{{module}}	country
{{Modules}}	Countries
{{modules}}	countries
{{variable}}	$country
{{collection}}	$countries
{{table}}	countries
{{route}}	countries
{{view}}	countries
{{policy}}	CountryPolicy
{{requestStore}}	StoreCountryRequest
{{requestUpdate}}
