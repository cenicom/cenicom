<?php

namespace App\Models;

class Currency extends BaseModel
{
    protected $table = 'currencies';

    protected $fillable = [

        'country_id',

        'code',

        'name',

        'symbol',

        'decimal_places',

        'status',

    ];
}
