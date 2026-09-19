<?php

declare(strict_types=1);

namespace App\Modules\Currency\Policies;

use App\Models\User;
use App\Modules\Currency\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización.
 *
 * @package App\Modules\Currency\Policies
 */
final class CurrencyPolicy
{
    /**
     * Determina si el usuario puede ver cualquier registro.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
    
    /**
     * Determina si el usuario puede ver el registro.
     */
    public function view(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede crear registros.
     */
    public function create(User $user): bool
    {
        return true;
    }
    
    /**
     * Determina si el usuario puede actualizar el registro.
     */
    public function update(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar el registro.
     */
    public function delete(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede restaurar el registro.
     */
    public function restore(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar definitivamente el registro.
     */
    public function forceDelete(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
}
