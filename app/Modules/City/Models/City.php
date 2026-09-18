<?php

declare(strict_types=1);

namespace App\Modules\City\Models;

use App\Modules\State\Models\State;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * City module
 *
 * @package App\Modules\City\Models
 */
final class City extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'cities';

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

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */



}
