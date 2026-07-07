<?php

namespace App\Models;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'legal_name',
        'tax_id',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'active',
        'sort_order',
    ];

    protected $casts = [];

    // 1. Traits

    // 2. Fillable

    // 3. Casts

    // 4. Relaciones
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // 5. Scopes

    // 6. Accessors

    // 7. Mutators

    // 8. Métodos auxiliares
}
