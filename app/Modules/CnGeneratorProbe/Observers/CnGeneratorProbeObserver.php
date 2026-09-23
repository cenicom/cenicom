<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Observers;

use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;
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
 * @package App\Modules\CnGeneratorProbe\Observers
 */
final class CnGeneratorProbeObserver
{
    /**
     * Handle the CnGeneratorProbe "creating" event.
     */
    public function creating(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento creating().
    }
    
    /**
     * Handle the CnGeneratorProbe "created" event.
     */
    public function created(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento created().
    }
    
    /**
     * Handle the CnGeneratorProbe "updating" event.
     */
    public function updating(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento updating().
    }
    
    /**
     * Handle the CnGeneratorProbe "updated" event.
     */
    public function updated(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento updated().
    }
    
    /**
     * Handle the CnGeneratorProbe "deleting" event.
     */
    public function deleting(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento deleting().
    }
    
    /**
     * Handle the CnGeneratorProbe "deleted" event.
     */
    public function deleted(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento deleted().
    }
    
    /**
     * Handle the CnGeneratorProbe "restoring" event.
     */
    public function restoring(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento restoring().
    }
    
    /**
     * Handle the CnGeneratorProbe "restored" event.
     */
    public function restored(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento restored().
    }
    
    /**
     * Handle the CnGeneratorProbe "force deleted" event.
     */
    public function forceDeleted(CnGeneratorProbe $cnGeneratorProbe): void
    {
        // TODO: Implementar lógica del evento forceDeleted().
    }
}
