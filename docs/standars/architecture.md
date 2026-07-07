# Arquitectura CN Framework

## Regla 1

El Framework nunca conocerá la lógica del ERP.

---

## Regla 2

Los módulos nunca accederán directamente entre sí.

Siempre utilizarán servicios o contratos.

---

## Regla 3

Todo componente reutilizable pertenece al Framework.

---

## Regla 4

Todo módulo debe registrarse en ModuleRegistry.

---

## Regla 5

Los archivos config solo contienen configuración.

Nunca lógica.

---

## Regla 6

Los Managers ejecutan lógica.

Los Registries almacenan información.

Los Generators crean código.

Los Providers registran servicios.
