# NAV-INT-01 — Cierre de verificación del registro de navegación

**Fecha:** 10 de octubre de 2026  
**Estado:** Verificación técnica completada  
**Método:** Evidencia → contrato → impacto → corrección → evidencia

## 1. Objetivo

Verificar que cada definición de navegación habilitada se registre una sola vez durante el arranque normal de Laravel, distinguiendo el registro de definiciones de la inicialización de manifiestos.

## 2. Contrato verificado

- Las definiciones de navegación se cargan desde los módulos habilitados.
- El registro central conserva definiciones únicas.
- `NavigationBootstrapper` resuelve cada definición y ejecuta su método `register()`.
- `NavigationManifestBootstrapper` delega la inicialización a `NavigationManifestDiscoveryInterface::discover()`.
- El registro de definiciones y la inicialización de manifiestos son responsabilidades distintas.
- No se realizarán cambios adicionales de producción sin evidencia de un defecto.

## 3. Cambios relacionados

- `app/Core/Navigation/Registry/NavigationDefinitionRegistry.php`: evita registrar dos veces la misma definición.
- `app/Modules/Institution/Providers/InstitutionServiceProvider.php`: elimina el registro directo de `InstitutionNavigation`, dejando esa responsabilidad en el flujo central.
- `tests/Unit/Core/Navigation/NavigationDefinitionLoaderTest.php`: comprueba que una definición repetida no se registre dos veces.
- `tests/Unit/Core/Navigation/NavigationDefinitionRegistryTest.php`: comprueba la unicidad de las definiciones.
- `tests/Feature/Core/Navigation/NavigationDefinitionRegistrationBootTest.php`: instrumenta las definiciones resueltas durante el arranque y cuenta las invocaciones observadas de `register()`.

## 4. Evidencia de pruebas

| Prueba | Resultado |
|---|---|
| `NavigationDefinitionRegistryTest` | 3 pruebas aprobadas; 3 aserciones |
| `NavigationDefinitionLoaderTest` | 4 pruebas aprobadas; 4 aserciones |
| Suite `tests/Unit/Core/Navigation` | 103 pruebas aprobadas; 254 aserciones |
| `NavigationDefinitionRegistrationBootTest` | 1 prueba aprobada; 6 aserciones |

Los resultados corresponden a las ejecuciones locales comunicadas durante esta revisión.

## 5. Inspección de la ruta de registro

La inspección del código identificó la llamada a `NavigationDefinitionInterface::register()` en:

`app/Core/Navigation/Bootstrap/NavigationBootstrapper.php`

El proveedor central ejecuta el cargador, el bootstrapper de definiciones y el bootstrapper de manifiestos en ese orden.

La búsqueda realizada en los proveedores de módulos y en los archivos de arranque consultados no encontró referencias directas a `InstitutionNavigation`, `InventoryNavigation` ni `NavigationDefinitionInterface`.

Esta inspección respalda la ruta central observada, pero no demuestra la ausencia absoluta de cualquier mecanismo indirecto fuera del alcance inspeccionado.

## 6. Alcance y limitaciones de la instrumentación

La prueba instrumenta las clases de definición resueltas mediante el contenedor de Laravel y cuenta las invocaciones observadas de `register()`.

La prueba verifica que:

- Existan definiciones para comprobar.
- El registro no contenga duplicados.
- Todas las definiciones esperadas queden instrumentadas.
- Las definiciones con invocaciones observadas coincidan con las esperadas.
- Cada definición habilitada invoque `register()` exactamente una vez.

La instrumentación no garantiza detectar llamadas directas que eludan el contenedor, por ejemplo, mediante la construcción explícita de objetos. La conclusión debe interpretarse dentro del alcance de la instrumentación y de la inspección realizada.

## 7. Archivo de prueba eliminado

`tests/Unit/Core/Navigation/NavigationServiceProviderTest.php` figura como eliminado en el estado actual de Git.

La comprobación histórica con `git cat-file -s` para el commit `9d99425` devolvió `0`, lo que confirma que el archivo tenía cero bytes en ese commit. Esta evidencia no muestra pérdida de contenido de prueba en dicha versión histórica.

## 8. Integridad del trabajo ajeno

Este cierre no autoriza a modificar, eliminar, restaurar ni reescribir:

- `docs/09102026.md`
- `phpunit.aca-year02.xml`
- `tests/Support/AcademicYear02DatabaseGuard.php`
- `tests/Support/AcademicYear02TestBootstrap.php`
- `tests/Support/AcademicYear02TestCase.php`
- Los demás archivos pendientes relacionados con ACA-YEAR-02.

Los cambios enumerados en esta sección deben conservarse y gestionarse por separado.

## 9. Decisión

Se considera completada la verificación técnica de NAV-INT-01 con base en las pruebas comunicadas y la inspección realizada.

No se requieren más cambios de producción para esta intervención. La conclusión queda limitada al contrato comprobado, a la cobertura de las pruebas y al alcance de la instrumentación descrito anteriormente.

**Principio de trabajo:** evidencia → contrato → impacto → corrección → evidencia.

**Lema:** ¡Primero el contrato; después el código! ¡Izar velas!
