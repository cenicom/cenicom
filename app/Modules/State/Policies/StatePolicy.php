<?php

declare(strict_types=1);

namespace App\Modules\State\Policies;

use App\Models\User;
use App\Modules\State\Models\State;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización.
 *
 * @package App\Modules\State\Policies
 */
final class StatePolicy
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
        State $state
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
        State $state
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar el registro.
     */
    public function delete(
        User $user,
        State $state
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede restaurar el registro.
     */
    public function restore(
        User $user,
        State $state
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar definitivamente el registro.
     */
    public function forceDelete(
        User $user,
        State $state
    ): bool {
        return true;
    }
}
