<?php

namespace App\Console\Commands\CN;

use Illuminate\Console\Command;

class AboutCommand extends Command
{
    protected $signature = 'cn:about';

    protected $description = 'Información del Framework Cenicom';

    public function handle(): int
    {
        $this->newLine();

        $this->info('===============================');
        $this->info('      CENICOM FRAMEWORK');
        $this->info('===============================');

        $this->newLine();

        $this->table(
            ['Propiedad', 'Valor'],
            [
                ['Framework', config('cn.app.name')],
                ['Versión', config('cn.app.version')],
                ['Build', config('cn.app.build')],
                ['Empresa', config('cn.app.company')],
                ['Laravel', app()->version()],
                ['PHP', PHP_VERSION],
                ['Entorno', app()->environment()],
            ]
        );

        return self::SUCCESS;
    }
}
