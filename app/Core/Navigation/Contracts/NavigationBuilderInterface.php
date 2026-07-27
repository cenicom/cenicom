<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;

interface NavigationBuilderInterface
{
    /**
     * Construye el árbol maestro de navegación.
     */
    public function build(): NavigationTreeData;
}
