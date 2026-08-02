<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery\Contracts;

interface NavigationManifestLoaderInterface
{
    /**
     * @return callable
     */
    public function load(
        string $path
    ): callable;
}
