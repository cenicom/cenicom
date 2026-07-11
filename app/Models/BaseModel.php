<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Modelo base del sistema.
 *
 * Centraliza el comportamiento común de todas las
 * entidades del ERP.
 *
 * @package App\Models
 * @since 1.0.0
 */
abstract class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    /**
     * Conversión automática de atributos.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
