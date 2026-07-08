<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
abstract class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;


    /**
     * Los atributos protegidos contra asignación masiva.
     */
    protected $guarded = [];

    /**
     * Conversión automática de tipos.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
