<?php

declare(strict_types=1);

namespace App\Modules\State\Models;

use App\Modules\City\Models\City;
use App\Modules\Country\Models\Country;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * State module
 *
 * @package App\Modules\State\Models
 */
final class State extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'states';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'country_id',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @var array<string,string>
     */
    protected function casts(): array
    {
        return [];
    }



    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
}
