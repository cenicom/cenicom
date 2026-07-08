<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

/**
 * Currency Model.
 *
 * Representa una moneda utilizada por el ERP.
 *
 * @property int $id
 * @property string $uuid
 * @property string $code
 * @property string $symbol
 * @property string $name
 * @property int $decimal_places
 * @property string $decimal_separator
 * @property string $thousands_separator
 * @property string $symbol_position
 * @property bool $is_default
 * @property bool $status
 */

class Currency extends BaseModel
{
    //use HasUuids;
    //use SoftDeletes;

    public const SYMBOL_BEFORE = 'before';

    public const SYMBOL_AFTER = 'after';

    public const STATUS_ACTIVE = true;

    public const STATUS_INACTIVE = false;

    public const DECIMAL_SEPARATOR = '.';

    public const THOUSANDS_SEPARATOR = ',';

    /**
     * Nombre de la tabla.
     */
    protected $table = 'currencies';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id';

    /**
     * Tipo de la clave primaria.
     */
    protected $keyType = 'int';

    /**
     * Indica si la clave primaria es autoincremental.
     */
    public $incrementing = true;

    /**
     * Asignación masiva.
     */
    protected $fillable = [
        'uuid',
        'code',
        'symbol',
        'name',
        'decimal_places',
        'decimal_separator',
        'thousands_separator',
        'symbol_position',
        'is_default',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Conversión de atributos.
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',

            'is_default' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name')
            ->orderBy('code');
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', strtoupper($code));
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where('status', true);
    }

    public function scopeInactive(
        Builder $query
    ): Builder {
        return $query->where('status', false);
    }

    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDefault(): bool
    {
        return (bool) $this->is_default;
    }

    public function displayName(): string
    {
        return sprintf(
            '%s (%s)',
            $this->name,
            $this->code
        );
    }

    public function formatExample(): string
    {
        $number = number_format(
            1234567.89,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        return $this->symbol_position === self::SYMBOL_BEFORE
            ? "{$this->symbol}{$number}"
            : "{$number}{$this->symbol}";
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function __toString(): string
    {
        return $this->displayName();
    }

    public function formattedSymbol(): string
    {
        return "{$this->symbol} {$this->code}";
    }


    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
