<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;

class CurrencyPolicy
{
    /**
     * Ver listado.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Ver un registro.
     */
    public function view(User $user, Currency $currency): bool
    {
        return true;
    }

    /**
     * Crear.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Actualizar.
     */
    public function update(User $user, Currency $currency): bool
    {
        return true;
    }

    /**
     * Eliminar.
     */
    public function delete(User $user, Currency $currency): bool
    {
        return ! $currency->isDefault();
    }

    /**
     * Restaurar.
     */
    public function restore(User $user, Currency $currency): bool
    {
        return true;
    }

    /**
     * Eliminar definitivamente.
     */
    public function forceDelete(User $user, Currency $currency): bool
    {
        return false;
    }
}
