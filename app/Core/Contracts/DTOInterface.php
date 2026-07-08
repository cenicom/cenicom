<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface DTOInterface
{
    /**
     * Crear DTO desde un arreglo de datos.
     */
    public static function fromArray(array $data): static;

    /**
     * Convertir DTO a arreglo.
     */
    public function toArray(): array;

    /**
     * Convertir DTO a JSON.
     */
    public function toJson(): string;
}
