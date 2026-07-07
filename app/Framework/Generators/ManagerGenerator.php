<?php

namespace App\Support\Generators;

class ManagerGenerator extends BaseGenerator
{
    public function generate(string $name): void
    {
        $stub = $this->stub('manager');

        $content = $this->render($stub, [

            'namespace' => 'App\\Support\\Managers',

            'class' => "{$name}Manager",

        ]);

        $this->write(

            app_path("Support/Managers/{$name}Manager.php"),

            $content

        );
    }
}
