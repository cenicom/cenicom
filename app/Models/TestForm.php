<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * 
 *
 * @package App\Models
 */
final class TestForm extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'test_forms';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @var array<string,string>
     */
    protected $casts = [

    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */



    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */



}
