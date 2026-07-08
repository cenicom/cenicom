<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUuid
{
    /**
     * Inicializa el Trait.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {

            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

        });
    }

    /**
     * Obtiene el UUID del modelo.
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
}
