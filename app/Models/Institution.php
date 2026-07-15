<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Module Institution
 *
 * @package App\Models
 */
final class Institution extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'institutions';

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
