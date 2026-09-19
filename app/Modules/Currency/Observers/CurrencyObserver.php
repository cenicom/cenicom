<?php

declare(strict_types=1);

namespace App\Modules\Currency\Observers;

use App\Modules\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Observer del módulo.
 *
 * Gestiona los eventos del ciclo de vida del modelo.
 *
 * @package App\Modules\Currency\Observers
 */
final class CurrencyObserver
{
    /**
     * Handle the Currency "creating" event.
     */
    public function creating(Currency $currency): void
    {
        // TODO: Implementar lógica del evento creating().
    }
    
    /**
     * Handle the Currency "created" event.
     */
    public function created(Currency $currency): void
    {
        // TODO: Implementar lógica del evento created().
    }
    
    /**
     * Handle the Currency "updating" event.
     */
    public function updating(Currency $currency): void
    {
        // TODO: Implementar lógica del evento updating().
    }
    
    /**
     * Handle the Currency "updated" event.
     */
    public function updated(Currency $currency): void
    {
        // TODO: Implementar lógica del evento updated().
    }
    
    /**
     * Handle the Currency "deleting" event.
     */
    public function deleting(Currency $currency): void
    {
        // TODO: Implementar lógica del evento deleting().
    }
    
    /**
     * Handle the Currency "deleted" event.
     */
    public function deleted(Currency $currency): void
    {
        // TODO: Implementar lógica del evento deleted().
    }
    
    /**
     * Handle the Currency "restoring" event.
     */
    public function restoring(Currency $currency): void
    {
        // TODO: Implementar lógica del evento restoring().
    }
    
    /**
     * Handle the Currency "restored" event.
     */
    public function restored(Currency $currency): void
    {
        // TODO: Implementar lógica del evento restored().
    }
    
    /**
     * Handle the Currency "force deleted" event.
     */
    public function forceDeleted(Currency $currency): void
    {
        // TODO: Implementar lógica del evento forceDeleted().
    }
}
