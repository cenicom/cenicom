<?php

namespace App\Support\Registries;

use App\Core\Foundation\Registry;
use Illuminate\Support\Facades\File;

class ModuleRegistry extends Registry
{
    public function load(): static
    {
        $path = config_path('cn/modules');

        if (! File::exists($path)) {
            return $this;
        }

        foreach (File::files($path) as $file) {

            $module = require $file->getRealPath();

            if (! isset($module['id'])) {
                continue;
            }

            $this->set($module['id'], $module);

        }

        ksort($this->items);

        return $this;
    }
}
