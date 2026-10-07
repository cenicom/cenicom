<?php

declare(strict_types=1);

namespace App\Modules\Campus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

final class Campus extends Model
{
    use HasUlids;

    protected $table = 'campuses';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'institution_id',
        'code',
        'short_code',
        'name',
        'ministry_code',
        'address_id',
        'status',
    ];
}
