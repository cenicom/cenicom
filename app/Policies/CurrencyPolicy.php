<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

use App\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Política de autorización para Currency.
 *
 * @package App\Policies
 */
final class CurrencyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }

    public function delete(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }

    public function restore(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }

    public function forceDelete(
        User $user,
        Currency $currency
    ): bool {
        return true;
    }
}
