<?php

namespace App\Support\Discovery;

use Illuminate\Support\Facades\File;

class ModuleDiscovery
{
    public function discover(): array
    {
        $modulesPath = app_path('Modules');

        if (! File::exists($modulesPath)) {
            return [];
        }

        return File::directories($modulesPath);
    }
}
