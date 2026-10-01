<?php

declare(strict_types=1);

namespace App\Modules\Address\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

final class Address extends Model
{
    use HasUlids;

    protected $table = 'addresses';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'neighborhood',
    ];
}
