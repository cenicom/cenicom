<?php

declare(strict_types=1);

namespace App\Modules\Currency\Models;

use App\Modules\Country\Models\Country;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Module Currency
 *
 * @package App\Modules\Currency\Models
 */
final class Currency extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'currencies';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'code',
        'precision',
        'symbol',
        'decimal_mark',
        'thousands_separator',
    ];

    /**
     * Conversión automática de atributos.
     *
     * @return array<string,string>
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

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
}
