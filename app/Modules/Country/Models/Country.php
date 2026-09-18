<?php

declare(strict_types=1);

namespace App\Modules\Country\Models;

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
 * Country module
 *
 * @package App\Modules\Country\Models
 */
final class Country extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'countries';

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

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */



}
