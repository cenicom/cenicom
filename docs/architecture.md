# Arquitectura del ERP Cenicom

Versión: 1.0.0

---

# Objetivo

Definir la arquitectura, principios, convenciones y organización del ERP Cenicom para garantizar un desarrollo consistente, escalable y mantenible.

---

# Filosofía

Cenicom será un ERP:

- Modular
- Escalable
- Reutilizable
- Fácil de mantener
- Independiente del módulo desarrollado

Todo elemento reutilizable deberá convertirse en un componente.

Nunca se duplicará código.

---

# Arquitectura General

Se adopta una arquitectura basada en capas.

Presentación

↓

Controladores

↓

Services

↓

Repositories

↓

Models

↓

Base de Datos

---

# Capas

## Presentación

Responsable de:

- Blade
- Componentes CN
- JavaScript
- CSS

No contiene lógica de negocio.

---

## Controllers

Responsable de:

- Recibir solicitudes
- Validar permisos
- Llamar Services
- Retornar respuestas

No contiene consultas SQL.

No contiene lógica de negocio.

---

## Services

Responsable de:

- Reglas de negocio
- Procesos
- Validaciones complejas
- Transacciones

Toda la lógica del ERP vive aquí.

---

## Repositories

Responsable de:

- Consultas
- Persistencia
- Acceso a datos

No contiene lógica de negocio.

---

## Models

Representan entidades.

No deben contener procesos extensos.

---

# Organización

app/

Actions

Enums

Helpers

Http

Models

Modules

Repositories

Services

Support

Traits

View

---

resources/

css/

js/

views/

---

docs/

Arquitectura

Roadmap

Convenciones

CN UI

Base de datos

API

---

# Componentes

Todo componente reutilizable pertenecerá al namespace:

x-cn

Ejemplo

<x-cn.button>

<x-cn.card>

<x-cn.forms.input>

<x-cn.layout.page-header>

---

# CSS

Todas las clases iniciarán con:

cn-

Ejemplo

.cn-card

.cn-button

.cn-table

.cn-sidebar

.cn-modal

---

# Variables CSS

Todas iniciarán con:

--cn-

Ejemplo

--cn-primary

--cn-danger

--cn-spacing-4

---

# JavaScript

Todo JS propio estará en:

resources/js/cenicom

No se escribirá JavaScript inline.

---

# Módulos

Cada módulo será independiente.

Ejemplo

Compras

Ventas

Inventario

CRM

Académico

Configuración

Cada módulo podrá tener:

Controllers

Services

Repositories

Policies

Requests

Views

---

# Componentes

Los componentes deben ser:

Reutilizables

Configurables

Documentados

Sin dependencias innecesarias

---

# Convenciones

Un archivo

Una responsabilidad

Una clase

Una función

---

# Objetivo Final

Que cualquier desarrollador pueda entender el proyecto en pocos minutos.
