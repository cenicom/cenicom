# CENICOM UI - Button v1.0

## Estado

Estable

## Objetivo

Proporcionar un botón reutilizable y consistente para todas las acciones del ERP CENICOM.

## Características

* Renderiza automáticamente `<button>` o `<a>`.
* Compatible con Bootstrap 5.
* Soporta iconos Font Awesome.
* Permite colores corporativos.
* Permite diferentes tamaños.
* Soporta atributos HTML mediante `{{ $attributes }}`.

## Ejemplos

### Botón simple

```blade
<x-ui.button>
    Guardar
</x-ui.button>
```

### Botón con icono

```blade
<x-ui.button
    color="success"
    icon="floppy-disk">
    Guardar
</x-ui.button>
```

### Enlace

```blade
<x-ui.button
    :href="route('countries.create')"
    icon="plus">
    Nuevo País
</x-ui.button>
```

## Historial

### v1.0

* Creación inicial del componente.
