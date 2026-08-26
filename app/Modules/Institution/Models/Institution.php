<?php

declare(strict_types=1);

namespace App\Modules\Institution\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Modelo Eloquent de Institution.
 *
 * Representa la persistencia de la entidad Institution
 * dentro del módulo Institution.
 *
 * @package App\Modules\Institution\Models
 * @since 1.0.0
 */
final class Institution extends Model
{
    use HasUlids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'institutions';

    /**
     * La clave primaria utiliza ULID.
     */
    protected $keyType = 'string';

    /**
     * La clave primaria no es autoincremental.
     */
    public $incrementing = false;

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'code',
        'name',
        'official_registration_country',
        'official_registration_authority',
        'official_registration_value',
        'status',
    ];
}
