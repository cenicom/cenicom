<?php

declare(strict_types=1);

namespace App\Modules\City\Observers;

use App\Modules\City\Models\City;
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
 * @package App\Modules\City\Observers
 */
final class CityObserver
{
    /**
     * Handle the City "creating" event.
     */
    public function creating(City $city): void
    {
        // TODO: Implementar lógica del evento creating().
    }
    
    /**
     * Handle the City "created" event.
     */
    public function created(City $city): void
    {
        // TODO: Implementar lógica del evento created().
    }
    
    /**
     * Handle the City "updating" event.
     */
    public function updating(City $city): void
    {
        // TODO: Implementar lógica del evento updating().
    }
    
    /**
     * Handle the City "updated" event.
     */
    public function updated(City $city): void
    {
        // TODO: Implementar lógica del evento updated().
    }
    
    /**
     * Handle the City "deleting" event.
     */
    public function deleting(City $city): void
    {
        // TODO: Implementar lógica del evento deleting().
    }
    
    /**
     * Handle the City "deleted" event.
     */
    public function deleted(City $city): void
    {
        // TODO: Implementar lógica del evento deleted().
    }
    
    /**
     * Handle the City "restoring" event.
     */
    public function restoring(City $city): void
    {
        // TODO: Implementar lógica del evento restoring().
    }
    
    /**
     * Handle the City "restored" event.
     */
    public function restored(City $city): void
    {
        // TODO: Implementar lógica del evento restored().
    }
    
    /**
     * Handle the City "force deleted" event.
     */
    public function forceDeleted(City $city): void
    {
        // TODO: Implementar lógica del evento forceDeleted().
    }
}
