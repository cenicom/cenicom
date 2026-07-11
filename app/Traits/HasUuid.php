<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente un UUID para los modelos
 * que utilicen este trait.
 *
 * @package App\Traits
 * @since 1.0.0
 */
trait HasUuid
{
    /**
     * Inicializa el trait.
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
