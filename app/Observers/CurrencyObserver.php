<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Observer del módulo Currency.
 *
 * Gestiona los eventos del ciclo de vida del modelo.
 *
 * @package App\Observers
 */
final class CurrencyObserver
{
    public function creating(Currency $currency): void
    {
        //
    }

    public function created(Currency $currency): void
    {
        //
    }

    public function updating(Currency $currency): void
    {
        //
    }

    public function updated(Currency $currency): void
    {
        //
    }

    public function deleting(Currency $currency): void
    {
        //
    }

    public function deleted(Currency $currency): void
    {
        //
    }

    public function restoring(Currency $currency): void
    {
        //
    }

    public function restored(Currency $currency): void
    {
        //
    }
}
