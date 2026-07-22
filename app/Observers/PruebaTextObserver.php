<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PruebaText;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Observer del módulo PruebaText.
 *
 * Gestiona los eventos del ciclo de vida del modelo.
 *
 * @package App\Observers
 */
final class PruebaTextObserver
{
    public function creating(PruebaText $pruebaText): void
    {
        //
    }

    public function created(PruebaText $pruebaText): void
    {
        //
    }

    public function updating(PruebaText $pruebaText): void
    {
        //
    }

    public function updated(PruebaText $pruebaText): void
    {
        //
    }

    public function deleting(PruebaText $pruebaText): void
    {
        //
    }

    public function deleted(PruebaText $pruebaText): void
    {
        //
    }

    public function restoring(PruebaText $pruebaText): void
    {
        //
    }

    public function restored(PruebaText $pruebaText): void
    {
        //
    }
}
