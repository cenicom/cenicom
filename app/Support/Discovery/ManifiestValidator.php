<?php

namespace App\Support\Discovery;

use App\Support\Exceptions\ModuleManifestException;

class ManifestValidator
{
    protected array $required = [

        'id',

        'name',

        'version',

        'enabled',

    ];

    public function validate(array $manifest): void
    {
        foreach ($this->required as $field) {

            if (! array_key_exists($field, $manifest)) {

                throw new ModuleManifestException(
                    "Falta el campo '{$field}' en module.php"
                );

            }

        }
    }
}
