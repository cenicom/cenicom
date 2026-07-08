<?php

declare(strict_types=1);

namespace App\Core\Traits;

use App\Core\Enums\Status;
use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    /**
     * Scope: registros activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope: registros inactivos.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    /**
     * Activar registro.
     */
    public function activate(): bool
    {
        $this->update([
            'status' => Status::ACTIVE->value,
        ]);
    }

    /**
     * Desactivar registro.
     */
    public function deactivate(): bool
    {
        return $this->update([
            'status' => false,
        ]);
    }

    /**
     * Cambiar estado.
     */
    public function toggleStatus(): bool
    {
        return $this->update([
            'status' => !$this->status,
        ]);
    }

    /**
     * ¿Está activo?
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * ¿Está inactivo?
     */
    public function isInactive(): bool
    {
        return !$this->isActive();
    }
}
