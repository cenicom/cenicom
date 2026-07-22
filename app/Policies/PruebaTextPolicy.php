<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

use App\Models\PruebaText;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización para PruebaText.
 *
 * @package App\Policies
 */
final class PruebaTextPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(
        User $user,
        PruebaText $pruebaText
    ): bool {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        PruebaText $pruebaText
    ): bool {
        return true;
    }

    public function delete(
        User $user,
        PruebaText $pruebaText
    ): bool {
        return true;
    }

    public function restore(
        User $user,
        PruebaText $pruebaText
    ): bool {
        return true;
    }

    public function forceDelete(
        User $user,
        PruebaText $pruebaText
    ): bool {
        return true;
    }
}
