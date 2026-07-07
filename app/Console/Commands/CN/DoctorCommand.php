<?php

namespace App\Console\Commands\CN;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DoctorCommand extends Command
{
    protected $signature = 'cn:doctor';

    protected $description = 'Diagnóstico del Framework Cenicom';

    public function handle(): int
    {
        $checks = [

            'config/cn' => base_path('config/cn'),

            'docs' => base_path('docs'),

            'resources/css/cenicom' => resource_path('css/cenicom'),

            'resources/js/cenicom' => resource_path('js/cenicom'),

            'stubs/cn' => base_path('stubs/cn'),

        ];

        $rows = [];

        foreach ($checks as $label => $path) {

            $rows[] = [

                File::exists($path) ? '✔' : '✖',

                $label,

            ];

        }

        $this->table(

            ['Estado', 'Elemento'],

            $rows

        );

        return self::SUCCESS;
    }
}
