<?php

namespace App\Support\Discovery;

class ManifestLoader
{
    public function load(string $modulePath): ?array
    {
        $manifest = $modulePath.'/Config/module.php';

        if (! file_exists($manifest)) {
            return null;
        }

        return require $manifest;
    }
}
