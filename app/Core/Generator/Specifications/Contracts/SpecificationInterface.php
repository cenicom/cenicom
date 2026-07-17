<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Contracts;

interface SpecificationInterface
{
    /**
     * Devuelve toda la especificación.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Información de identidad del módulo.
     *
     * @return array<string, mixed>
     */
    public function identity(): array;

    /**
     * Configuración de base de datos.
     *
     * @return array<string, mixed>
     */
    public function database(): array;

    /**
     * Definición de campos.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fields(): array;

    /**
     * Relaciones del módulo.
     *
     * @return array<string, mixed>
     */
    public function relations(): array;

    public function columns(): array;

    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function validation(): array;

    /**
     * Permisos.
     *
     * @return array<string, mixed>
     */
    public function permissions(): array;

    /**
     * Configuración de navegación.
     *
     * @return array<string, mixed>
     */
    public function navigation(): array;

    /**
     * Opciones de generación.
     *
     * @return array<string, mixed>
     */
    public function generation(): array;

    /**
     * Metadatos de la especificación.
     *
     * @return array<string, mixed>
     */
    public function metadata(): array;

    /**
     * Versión del estándar.
     */
    public function version(): string;
}
