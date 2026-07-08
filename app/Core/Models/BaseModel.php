<?php

declare(strict_types=1);

namespace App\Core\Models;

use App\Core\Traits\HasStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasUuids;
    use SoftDeletes;
    use HasStatus;

    /**
     * UUID como Route Key.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Conversión de atributos.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para registros activos.
     */
    public function scopeActive($query)
    {
        if (in_array('status', $this->getFillable())) {
            $query->where('status', true);
        }

        return $query;
    }

    /**
     * Scope para registros inactivos.
     */
    public function scopeInactive($query)
    {
        if (in_array('status', $this->getFillable())) {
            $query->where('status', false);
        }

        return $query;
    }

    /**
     * Scope ordenado por ID.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc($this->getQualifiedKeyName());
    }

    /**
     * Scope ordenado por creación.
     */
    public function scopeRecent($query)
    {
        return $query->latest();
    }

    /**
     * UUID público.
     */
    public function routeUrl(): string
    {
        return (string) $this->uuid;
    }

    /**
     * Estado del registro.
     */
    public function isActive(): bool
    {
        if (! in_array('status', $this->getFillable())) {
            return true;
        }

        return (bool) $this->status;
    }
}
