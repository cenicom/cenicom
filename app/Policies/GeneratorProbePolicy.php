<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\GeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización.
 *
 * @package App\Policies
 */
final class GeneratorProbePolicy
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
        GeneratorProbe $generatorProbe
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
        GeneratorProbe $generatorProbe
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar el registro.
     */
    public function delete(
        User $user,
        GeneratorProbe $generatorProbe
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede restaurar el registro.
     */
    public function restore(
        User $user,
        GeneratorProbe $generatorProbe
    ): bool {
        return true;
    }
    
    /**
     * Determina si el usuario puede eliminar definitivamente el registro.
     */
    public function forceDelete(
        User $user,
        GeneratorProbe $generatorProbe
    ): bool {
        return true;
    }
}
