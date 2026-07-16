<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Module TestModule
 *
 * @package App\Models
 */
final class TestModule extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'test_modules';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        
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
