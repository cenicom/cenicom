<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;

interface ModuleManifestFinderInterface
{
    /**
     * Obtiene la colección de manifiestos
     * disponibles en el sistema.
     *
     * @return iterable
     */
    public function find(): iterable;
}
