# ADR-001

## Título

Separación entre Framework y ERP.

## Estado

Aceptado

## Fecha

2026-07-07

## Decisión

Todo el código reutilizable vivirá en Core y Support.

Los módulos del ERP permanecerán aislados en app/Modules.

## Consecuencias

- Mayor mantenibilidad.
- Posibilidad de reutilizar el Framework.
- Menor acoplamiento.
