<?php

declare(strict_types=1);

namespace App\Modules\Campus\Models;

use App\Modules\Address\Models\Address;
use App\Modules\Institution\Models\Institution;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Institution::class,
            'institution_id',
        );
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(
            Address::class,
            'address_id',
        );
    }
}
