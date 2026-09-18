<?php

declare(strict_types=1);

namespace App\Modules\City\Policies;

use App\Models\User;
use App\Modules\City\Models\City;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización.
 *
 * @package App\Modules\City\Policies
 */
final class CityPolicy
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
        City $city
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
        City $city
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar el registro.
     */
    public function delete(
        User $user,
        City $city
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede restaurar el registro.
     */
    public function restore(
        User $user,
        City $city
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar definitivamente el registro.
     */
    public function forceDelete(
        User $user,
        City $city
    ): bool {
        return true;
    }
}
