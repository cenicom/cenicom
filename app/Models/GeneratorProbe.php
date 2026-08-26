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
 * Module GeneratorProbe
 *
 * @package App\Models
 */
final class GeneratorProbe extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'generator_probes';

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
    protected function casts(): array
{
    return [



    ];
}



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
