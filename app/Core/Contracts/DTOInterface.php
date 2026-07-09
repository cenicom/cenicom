<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface DTOInterface
{
    /**
     * Crea una nueva instancia del DTO a partir de un arreglo.
     *
     * @param array<string, mixed> $data
     */
    //public static function fromArray(array $data): static;
    public static function fromArray(array $data): static;

    /**
     * Convierte el DTO a un arreglo asociativo.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Convertir DTO a JSON.
     */
    public function toJson(): string;
}
