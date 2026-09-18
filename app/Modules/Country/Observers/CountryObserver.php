<?php

declare(strict_types=1);

namespace App\Modules\Country\Observers;

use App\Modules\Country\Models\Country;
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
 * @package App\Modules\Country\Observers
 */
final class CountryObserver
{
    /**
     * Handle the Country "creating" event.
     */
    public function creating(Country $country): void
    {
        // TODO: Implementar lógica del evento creating().
    }
    
    /**
     * Handle the Country "created" event.
     */
    public function created(Country $country): void
    {
        // TODO: Implementar lógica del evento created().
    }
    
    /**
     * Handle the Country "updating" event.
     */
    public function updating(Country $country): void
    {
        // TODO: Implementar lógica del evento updating().
    }
    
    /**
     * Handle the Country "updated" event.
     */
    public function updated(Country $country): void
    {
        // TODO: Implementar lógica del evento updated().
    }
    
    /**
     * Handle the Country "deleting" event.
     */
    public function deleting(Country $country): void
    {
        // TODO: Implementar lógica del evento deleting().
    }
    
    /**
     * Handle the Country "deleted" event.
     */
    public function deleted(Country $country): void
    {
        // TODO: Implementar lógica del evento deleted().
    }
    
    /**
     * Handle the Country "restoring" event.
     */
    public function restoring(Country $country): void
    {
        // TODO: Implementar lógica del evento restoring().
    }
    
    /**
     * Handle the Country "restored" event.
     */
    public function restored(Country $country): void
    {
        // TODO: Implementar lógica del evento restored().
    }
    
    /**
     * Handle the Country "force deleted" event.
     */
    public function forceDeleted(Country $country): void
    {
        // TODO: Implementar lógica del evento forceDeleted().
    }
}
